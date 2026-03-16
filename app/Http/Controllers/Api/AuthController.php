<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function sendVerificationCode(User $user): void
    {
        EmailVerification::where('user_id', $user->id)->delete();
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        EmailVerification::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
        ]);
        Mail::to($user->email)->send(new VerifyEmail($user, $code));
    }

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
        $this->sendVerificationCode($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token'   => $token,
            'token_type'     => 'Bearer',
            'user'           => $user,
            'email_verified' => false,
            'message'        => 'Inscription réussie ! Vérifiez votre email pour activer votre compte.',
        ], 201);
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.'], 200);
        }

        $verification = EmailVerification::where('user_id', $user->id)
            ->where('code', $request->code)
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Code invalide.'], 422);
        }

        if ($verification->isExpired()) {
            $verification->delete();
            return response()->json(['message' => 'Code expiré. Demandez un nouveau code.'], 410);
        }

        $user->markEmailAsVerified();
        $verification->delete();

        return response()->json([
            'message' => 'Email vérifié avec succès !',
            'user'    => $user->fresh()->load('roles'),
        ]);
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.'], 400);
        }
        $this->sendVerificationCode($user);
        return response()->json(['message' => 'Nouveau code envoyé.']);
    }

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
            'access_token'   => $token,
            'token_type'     => 'Bearer',
            'user'           => $user,
            'email_verified' => (bool) $user->email_verified_at,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user  = User::where('email', $request->email)->first();
        $token = Password::createToken($user);
        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new ResetPassword($user, $resetUrl));
        return response()->json(['message' => 'Un email de réinitialisation a été envoyé.']);
    }

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
                $user->tokens()->delete();
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