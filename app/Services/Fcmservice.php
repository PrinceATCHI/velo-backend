<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $serverKey;
    private string $projectId;

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key', '');
        $this->projectId = config('services.firebase.project_id', '');
    }

    /**
     * Envoyer une notification push à un utilisateur spécifique
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        if (empty($this->serverKey)) {
            Log::warning('FCM: server_key non configuré — push ignoré');
            return;
        }

        $tokens = FcmToken::where('user_id', $userId)->pluck('token')->toArray();

        if (empty($tokens)) {
            return; // Pas de device enregistré pour cet utilisateur
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoyer à une liste de tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        if (empty($this->serverKey) || empty($tokens)) return;

        // Batch par 500 (limite FCM)
        foreach (array_chunk($tokens, 500) as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'registration_ids' => $chunk,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'icon'  => '/icon-192.png',
                        'click_action' => config('app.frontend_url'),
                    ],
                    'data' => $data,
                ]);

                $result = $response->json();

                // Nettoyer les tokens invalides
                if (isset($result['results'])) {
                    foreach ($result['results'] as $i => $r) {
                        if (isset($r['error']) && in_array($r['error'], ['InvalidRegistration', 'NotRegistered'])) {
                            FcmToken::where('token', $chunk[$i])->delete();
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::error('FCM send error: ' . $e->getMessage());
            }
        }
    }
}