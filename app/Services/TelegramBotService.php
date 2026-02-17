<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function sendMessage(string|int $chatId, string $text, array $extraPayload = []): void
    {
        $botToken = (string) config('services.telegram.bot_token');

        if ($botToken === '') {
            Log::warning('Telegram bot token is missing. Message not sent.', [
                'chat_id' => $chatId,
            ]);

            return;
        }

        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $extraPayload);

        try {
            Http::timeout(10)
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", $payload)
                ->throw();
        } catch (\Throwable $exception) {
            Log::error('Failed to send Telegram message.', [
                'chat_id' => $chatId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
