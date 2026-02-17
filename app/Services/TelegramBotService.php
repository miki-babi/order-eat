<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function sendMessage(string|int $chatId, string $text, array $extraPayload = []): void
    {
        $botToken = (string) config('services.telegram.bot_token');

        Log::info('Telegram bot sendMessage started.', [
            'chat_id' => $chatId,
            'text_preview' => mb_substr($text, 0, 120),
            'has_extra_payload' => $extraPayload !== [],
            'extra_payload_keys' => array_keys($extraPayload),
        ]);

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
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", $payload)
                ->throw();

            Log::info('Telegram bot sendMessage completed.', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'ok' => $response->json('ok'),
                'telegram_message_id' => $response->json('result.message_id'),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to send Telegram message.', [
                'chat_id' => $chatId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
