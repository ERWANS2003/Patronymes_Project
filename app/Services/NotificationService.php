<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotification;

class NotificationService
{
    /**
     * Envoie une notification par email
     */
    public static function sendEmailNotification(string $title, string $message, array $context = [], string $level = 'info'): bool
    {
        if (!config('monitoring.alerts.email.enabled')) {
            return false;
        }

        try {
            $recipients = config('monitoring.alerts.email.recipients');

            if (empty($recipients)) {
                Log::warning('No email recipients configured for monitoring alerts');
                return false;
            }

            foreach ($recipients as $recipient) {
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($recipient)->send(new AlertNotification($title, $message, $context, $level));
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'error' => $e->getMessage(),
                'title' => $title,
                'level' => $level
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification Slack
     */
    public static function sendSlackNotification(string $title, string $message, array $context = [], string $level = 'info'): bool
    {
        if (!config('monitoring.alerts.slack.enabled')) {
            return false;
        }

        $webhookUrl = config('monitoring.alerts.slack.webhook_url');

        if (empty($webhookUrl)) {
            Log::warning('No Slack webhook URL configured for monitoring alerts');
            return false;
        }

        try {
            $color = self::getSlackColor($level);
            $emoji = self::getSlackEmoji($level);

            $payload = [
                'text' => "{$emoji} {$title}",
                'attachments' => [
                    [
                        'color' => $color,
                        'fields' => [
                            [
                                'title' => 'Message',
                                'value' => $message,
                                'short' => false
                            ],
                            [
                                'title' => 'Level',
                                'value' => strtoupper($level),
                                'short' => true
                            ],
                            [
                                'title' => 'Timestamp',
                                'value' => now()->toISOString(),
                                'short' => true
                            ]
                        ]
                    ]
                ]
            ];

            // Ajouter le contexte si disponible
            if (!empty($context)) {
                $payload['attachments'][0]['fields'][] = [
                    'title' => 'Context',
                    'value' => '```' . json_encode($context, JSON_PRETTY_PRINT) . '```',
                    'short' => false
                ];
            }

            $response = Http::post($webhookUrl, $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Slack notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Slack notification', [
                'error' => $e->getMessage(),
                'title' => $title,
                'level' => $level
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification webhook
     */
    public static function sendWebhookNotification(string $title, string $message, array $context = [], string $level = 'info'): bool
    {
        if (!config('monitoring.notifications.webhook')) {
            return false;
        }

        $webhookUrl = config('monitoring.notifications.webhook_url');

        if (empty($webhookUrl)) {
            Log::warning('No webhook URL configured for monitoring notifications');
            return false;
        }

        try {
            $payload = [
                'title' => $title,
                'message' => $message,
                'level' => $level,
                'timestamp' => now()->toISOString(),
                'context' => $context,
                'source' => config('app.name'),
                'environment' => config('app.env')
            ];

            $response = Http::post($webhookUrl, $payload);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Webhook notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send webhook notification', [
                'error' => $e->getMessage(),
                'title' => $title,
                'level' => $level
            ]);
            return false;
        }
    }

    /**
     * Envoie une notification multi-canal
     */
    public static function sendNotification(string $title, string $message, array $context = [], string $level = 'info'): array
    {
        $results = [];

        // Email
        if (config('monitoring.notifications.channels.email')) {
            $results['email'] = self::sendEmailNotification($title, $message, $context, $level);
        }

        // Slack
        if (config('monitoring.notifications.channels.slack')) {
            $results['slack'] = self::sendSlackNotification($title, $message, $context, $level);
        }

        // Webhook
        if (config('monitoring.notifications.channels.webhook')) {
            $results['webhook'] = self::sendWebhookNotification($title, $message, $context, $level);
        }

        return $results;
    }

    /**
     * Obtient la couleur Slack pour le niveau
     */
    private static function getSlackColor(string $level): string
    {
        return match ($level) {
            'critical' => 'danger',
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'good',
            default => '#36a64f'
        };
    }

    /**
     * Obtient l'emoji Slack pour le niveau
     */
    private static function getSlackEmoji(string $level): string
    {
        return match ($level) {
            'critical' => ':rotating_light:',
            'error' => ':x:',
            'warning' => ':warning:',
            'info' => ':information_source:',
            default => ':bell:'
        };
    }

    /**
     * Teste la configuration des notifications
     */
    public static function testNotifications(): array
    {
        $results = [];

        // Test email
        if (config('monitoring.notifications.channels.email')) {
            $results['email'] = self::sendEmailNotification(
                'Test de Notification',
                'Ceci est un test de notification par email.',
                ['test' => true],
                'info'
            );
        }

        // Test Slack
        if (config('monitoring.notifications.channels.slack')) {
            $results['slack'] = self::sendSlackNotification(
                'Test de Notification',
                'Ceci est un test de notification Slack.',
                ['test' => true],
                'info'
            );
        }

        // Test webhook
        if (config('monitoring.notifications.channels.webhook')) {
            $results['webhook'] = self::sendWebhookNotification(
                'Test de Notification',
                'Ceci est un test de notification webhook.',
                ['test' => true],
                'info'
            );
        }

        return $results;
    }
}
