<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─── Inscription ──────────────────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        // Envoyer l'email de vérification
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
            'message'      => 'Inscription réussie ! Vérifiez votre email pour activer votre compte.',
        ], 201);
    }

    // ─── Connexion ────────────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $user->load('roles');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    // ─── Déconnexion ──────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // ─── Profil ───────────────────────────────────────────────────────────────
    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }

    // ─── Renvoyer l'email de vérification ─────────────────────────────────────
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.'], 400);
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

        return response()->json(['message' => 'Email de vérification renvoyé.']);
    }

    // ─── Vérifier l'email ─────────────────────────────────────────────────────
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            return response()->json(['message' => 'Lien de vérification invalide.'], 403);
        }

        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'Lien expiré. Demandez un nouveau lien.'], 410);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.frontend_url') . '/login?verified=already');
        }

        $user->markEmailAsVerified();

        return redirect(config('app.frontend_url') . '/login?verified=1');
    }

    // ─── Demande de reset password ────────────────────────────────────────────
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Générer un token de reset
        $token = Password::createToken($user);

        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        Mail::to($user->email)->send(new ResetPassword($user, $resetUrl));

        return response()->json([
            'message' => 'Un email de réinitialisation a été envoyé.',
        ]);
    }

    // ─── Reset password ───────────────────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // Invalider tous les tokens existants
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Mot de passe réinitialisé avec succès.']);
        }

        return response()->json([
            'message' => match($status) {
                Password::INVALID_TOKEN => 'Token invalide ou expiré.',
                Password::INVALID_USER  => 'Aucun compte trouvé avec cet email.',
                default                 => 'Erreur lors de la réinitialisation.',
            }
        ], 400);
    }
}