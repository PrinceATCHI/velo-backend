<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    private $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('firebase-credentials.json');

            if (!file_exists($credentialsPath)) {
                Log::warning('FCM: firebase-credentials.json introuvable — push désactivé');
                return;
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();

        } catch (\Exception $e) {
            Log::error('FCM init error: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer une notification push à un utilisateur spécifique
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        if (!$this->messaging) return;

        $tokens = FcmToken::where('user_id', $userId)->pluck('token')->toArray();

        if (empty($tokens)) return;

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Envoyer à une liste de tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
{
    if (!$this->messaging || empty($tokens)) return;

    foreach ($tokens as $token) {
        try {
            $webpush = \Kreait\Firebase\Messaging\WebPushConfig::fromArray([
    'headers' => [
        'Urgency' => 'high',  // ← priorité haute
        'TTL'     => '60',    // ← expire après 60 secondes si pas livré
    ],
    'notification' => [
        'title'   => $title,
        'body'    => $body,
        'icon'    => '/icon-192.png',
        'vibrate' => [200, 100, 200],
        'requireInteraction' => true,
    ],
    'data' => array_map('strval', $data),
]);

            $message = \Kreait\Firebase\Messaging\CloudMessage::new()
                ->withToken($token)
                ->withWebPushConfig($webpush);

            $this->messaging->send($message);

            Log::info('FCM: notification envoyée à ' . substr($token, 0, 20) . '...');

        } catch (\Exception $e) {
            Log::error('FCM send error: ' . $e->getMessage());
            if (str_contains($e->getMessage(), 'UNREGISTERED')) {
                FcmToken::where('token', $token)->delete();
            }
        }
    }
}
}