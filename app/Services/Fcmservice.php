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

        // Convertir toutes les valeurs data en string (requis par FCM)
        $stringData = array_map('strval', $data);

        foreach (array_chunk($tokens, 500) as $chunk) {
            try {
                $message = CloudMessage::new()
                    ->withNotification(
                        Notification::create($title, $body)
                            ->withImageUrl('/icon-192.png')
                    )
                    ->withData($stringData);

                $report = $this->messaging->sendMulticast($message, $chunk);

                // Nettoyer les tokens invalides
                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        $invalidToken = $failure->target()->value();
                        FcmToken::where('token', $invalidToken)->delete();
                        Log::info('FCM: token invalide supprimé — ' . substr($invalidToken, 0, 20) . '...');
                    }
                }

                Log::info("FCM: {$report->successes()->count()} envoyés, {$report->failures()->count()} échoués");

            } catch (\Exception $e) {
                Log::error('FCM send error: ' . $e->getMessage());
            }
        }
    }
}