<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramBotService $telegramBotService,
    ) {
    }

    public function webhook(Request $request): JsonResponse
    {
        $update = $request->all();
        $message = $update['message'] ?? null;

        if (is_array($message)) {
            $this->handleMessage($message);
        }

        return response()->json(['ok' => true]);
    }

    public function customerPrefill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'telegram_user_id' => ['required', 'string', 'max:255'],
        ]);

        $customer = Customer::query()
            ->where('telegram_id', $validated['telegram_user_id'])
            ->latest('id')
            ->first();

        if (! $customer) {
            return response()->json([
                'customer' => null,
            ]);
        }

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'username' => $customer->username,
                'telegram_id' => $customer->telegram_id,
            ],
        ]);
    }

    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'] ?? null;

        if ($chatId === null) {
            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        if ($text !== '' && Str::startsWith($text, '/start')) {
            $this->askForContact((string) $chatId);

            return;
        }

        if (isset($message['contact']) && is_array($message['contact'])) {
            $this->handleContactMessage($message, (string) $chatId);
        }
    }

    private function askForContact(string $chatId): void
    {
        $this->telegramBotService->sendMessage(
            chatId: $chatId,
            text: 'Share your contact to place order and track order.',
            extraPayload: [
                'reply_markup' => [
                    'keyboard' => [
                        [
                            [
                                'text' => 'Share Contact',
                                'request_contact' => true,
                            ],
                        ],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                    'input_field_placeholder' => 'Tap to share your contact',
                ],
            ],
        );
    }

    private function handleContactMessage(array $message, string $chatId): void
    {
        $fromId = (string) ($message['from']['id'] ?? '');
        $contactUserId = (string) ($message['contact']['user_id'] ?? '');

        if ($fromId !== '' && $contactUserId !== '' && $fromId !== $contactUserId) {
            $this->telegramBotService->sendMessage(
                chatId: $chatId,
                text: 'Please share your own contact using the button below.',
            );

            $this->askForContact($chatId);

            return;
        }

        $this->upsertCustomerFromContact($message);

        $this->telegramBotService->sendMessage(
            chatId: $chatId,
            text: 'Contact saved successfully.',
            extraPayload: [
                'reply_markup' => [
                    'remove_keyboard' => true,
                ],
            ],
        );

        $miniAppUrl = trim((string) config('services.telegram.mini_app_url', ''));

        if ($miniAppUrl === '') {
            return;
        }

        $this->telegramBotService->sendMessage(
            chatId: $chatId,
            text: 'Open the menu to place your order:',
            extraPayload: [
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            [
                                'text' => 'Open Menu',
                                'web_app' => [
                                    'url' => $miniAppUrl,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        );
    }

    private function upsertCustomerFromContact(array $message): Customer
    {
        $from = is_array($message['from'] ?? null) ? $message['from'] : [];
        $contact = is_array($message['contact'] ?? null) ? $message['contact'] : [];

        $telegramId = (string) ($from['id'] ?? $contact['user_id'] ?? '');
        $phone = trim((string) ($contact['phone_number'] ?? ''));
        $telegramUsername = trim((string) ($from['username'] ?? ''));

        $firstName = trim((string) ($contact['first_name'] ?? $from['first_name'] ?? ''));
        $lastName = trim((string) ($contact['last_name'] ?? $from['last_name'] ?? ''));
        $name = trim($firstName.' '.$lastName);

        if ($name === '') {
            $name = 'Telegram Customer';
        }

        $customer = null;

        if ($telegramId !== '') {
            $customer = Customer::query()
                ->where('telegram_id', $telegramId)
                ->latest('id')
                ->first();
        }

        if (! $customer && $phone !== '') {
            $customer = Customer::query()
                ->where('phone', $phone)
                ->where(function ($query) use ($telegramId): void {
                    $query->whereNull('telegram_id');

                    if ($telegramId !== '') {
                        $query->orWhere('telegram_id', $telegramId);
                    }
                })
                ->latest('id')
                ->first();
        }

        if (! $customer) {
            $seedUsername = $telegramUsername !== ''
                ? $telegramUsername
                : ($telegramId !== '' ? 'tg_'.$telegramId : $name);

            $customer = new Customer([
                'username' => $this->generateUniqueUsername($seedUsername),
            ]);
        }

        if (! $customer->username) {
            $seedUsername = $telegramUsername !== ''
                ? $telegramUsername
                : ($telegramId !== '' ? 'tg_'.$telegramId : $name);

            $customer->username = $this->generateUniqueUsername($seedUsername);
        }

        $customer->name = $name;

        if ($phone !== '') {
            $customer->phone = $phone;
        }

        if ($telegramId !== '' && ($customer->telegram_id === null || $customer->telegram_id === $telegramId)) {
            $customer->telegram_id = $telegramId;
        }

        $customer->save();

        return $customer;
    }

    private function generateUniqueUsername(string $seed): string
    {
        $normalized = (string) Str::of($seed)
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_');

        $base = $normalized !== '' ? Str::limit($normalized, 24, '') : 'guest';
        $candidate = $base;
        $counter = 1;

        while (Customer::query()->where('username', $candidate)->exists()) {
            $suffix = '_'.$counter;
            $candidate = Str::limit($base, 24 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }
}
