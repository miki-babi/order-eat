<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\TelegramBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

        Log::info('Telegram webhook received update.', [
            'update_id' => $update['update_id'] ?? null,
            'has_message' => is_array($message),
            'update_keys' => array_keys($update),
            'chat_id' => is_array($message) ? ($message['chat']['id'] ?? null) : null,
            'from_id' => is_array($message) ? ($message['from']['id'] ?? null) : null,
            'has_contact' => is_array($message) && isset($message['contact']),
            'text' => is_array($message) ? Str::limit((string) ($message['text'] ?? ''), 120) : null,
        ]);

        if (is_array($message)) {
            Log::info('Telegram webhook dispatching message handler.', [
                'chat_id' => $message['chat']['id'] ?? null,
                'from_id' => $message['from']['id'] ?? null,
            ]);
            $this->handleMessage($message);

            Log::info('Telegram webhook message handler completed.', [
                'chat_id' => $message['chat']['id'] ?? null,
                'from_id' => $message['from']['id'] ?? null,
            ]);
        } else {
            Log::info('Telegram webhook update ignored because message payload is missing.');
        }

        Log::info('Telegram webhook returning success response.');

        return response()->json(['ok' => true]);
    }

    public function customerPrefill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'telegram_user_id' => ['required', 'string', 'max:255'],
        ]);

        Log::info('Telegram customer prefill lookup requested.', [
            'telegram_user_id' => $validated['telegram_user_id'],
        ]);

        $customer = Customer::query()
            ->where('telegram_id', $validated['telegram_user_id'])
            ->latest('id')
            ->first();

        if (! $customer) {
            Log::info('Telegram customer prefill customer not found.', [
                'telegram_user_id' => $validated['telegram_user_id'],
            ]);

            return response()->json([
                'customer' => null,
            ]);
        }

        Log::info('Telegram customer prefill customer found.', [
            'telegram_user_id' => $validated['telegram_user_id'],
            'customer_id' => $customer->id,
            'has_phone' => $customer->phone !== null && $customer->phone !== '',
        ]);

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

        Log::info('Telegram message handling started.', [
            'chat_id' => $chatId,
            'from_id' => $message['from']['id'] ?? null,
            'message_id' => $message['message_id'] ?? null,
            'has_text' => isset($message['text']),
            'has_contact' => isset($message['contact']),
        ]);

        if ($chatId === null) {
            Log::warning('Telegram message handling aborted because chat_id is missing.', [
                'message_id' => $message['message_id'] ?? null,
            ]);

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        if ($text !== '' && Str::startsWith($text, '/start')) {
            Log::info('Telegram /start received. Sending contact request.', [
                'chat_id' => $chatId,
                'from_id' => $message['from']['id'] ?? null,
            ]);
            $this->askForContact((string) $chatId);

            return;
        }

        if (isset($message['contact']) && is_array($message['contact'])) {
            Log::info('Telegram contact message received. Processing contact.', [
                'chat_id' => $chatId,
                'from_id' => $message['from']['id'] ?? null,
                'contact_user_id' => $message['contact']['user_id'] ?? null,
            ]);
            $this->handleContactMessage($message, (string) $chatId);

            return;
        }

        Log::info('Telegram message ignored because it is neither /start nor contact.', [
            'chat_id' => $chatId,
            'from_id' => $message['from']['id'] ?? null,
            'text' => Str::limit($text, 120),
        ]);
    }

    private function askForContact(string $chatId): void
    {
        Log::info('Telegram sending request_contact keyboard.', [
            'chat_id' => $chatId,
        ]);

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

        Log::info('Telegram request_contact keyboard send attempted.', [
            'chat_id' => $chatId,
        ]);
    }

    private function handleContactMessage(array $message, string $chatId): void
    {
        $fromId = (string) ($message['from']['id'] ?? '');
        $contactUserId = (string) ($message['contact']['user_id'] ?? '');

        Log::info('Telegram contact handling started.', [
            'chat_id' => $chatId,
            'from_id' => $fromId,
            'contact_user_id' => $contactUserId,
            'has_phone' => isset($message['contact']['phone_number']) && $message['contact']['phone_number'] !== '',
        ]);

        if ($fromId !== '' && $contactUserId !== '' && $fromId !== $contactUserId) {
            Log::warning('Telegram contact rejected because user shared a different contact.', [
                'chat_id' => $chatId,
                'from_id' => $fromId,
                'contact_user_id' => $contactUserId,
            ]);

            $this->telegramBotService->sendMessage(
                chatId: $chatId,
                text: 'Please share your own contact using the button below.',
            );

            $this->askForContact($chatId);

            return;
        }

        $customer = $this->upsertCustomerFromContact($message);

        Log::info('Telegram contact persisted to customer.', [
            'chat_id' => $chatId,
            'customer_id' => $customer->id,
            'customer_telegram_id' => $customer->telegram_id,
            'has_phone' => $customer->phone !== null && $customer->phone !== '',
        ]);

        $this->telegramBotService->sendMessage(
            chatId: $chatId,
            text: 'Contact saved successfully.',
            extraPayload: [
                'reply_markup' => [
                    'remove_keyboard' => true,
                ],
            ],
        );

        Log::info('Telegram sent contact saved confirmation message.', [
            'chat_id' => $chatId,
        ]);

        $miniAppUrl = trim((string) config('services.telegram.mini_app_url', ''));

        if ($miniAppUrl === '') {
            Log::warning('Telegram mini app url is missing. Inline button was not sent.', [
                'chat_id' => $chatId,
            ]);

            return;
        }

        Log::info('Telegram sending mini app launch button.', [
            'chat_id' => $chatId,
            'mini_app_url' => $miniAppUrl,
        ]);

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

        Log::info('Telegram mini app launch button send attempted.', [
            'chat_id' => $chatId,
        ]);
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

        Log::info('Telegram customer upsert started.', [
            'telegram_id' => $telegramId !== '' ? $telegramId : null,
            'telegram_username' => $telegramUsername !== '' ? $telegramUsername : null,
            'phone_present' => $phone !== '',
            'from_first_name' => $firstName !== '' ? $firstName : null,
            'from_last_name' => $lastName !== '' ? $lastName : null,
        ]);

        if ($name === '') {
            $name = 'Telegram Customer';

            Log::info('Telegram customer name fallback applied.', [
                'fallback_name' => $name,
            ]);
        }

        $customer = null;

        if ($telegramId !== '') {
            $customer = Customer::query()
                ->where('telegram_id', $telegramId)
                ->latest('id')
                ->first();

            Log::info('Telegram customer lookup by telegram_id completed.', [
                'telegram_id' => $telegramId,
                'customer_found' => $customer !== null,
                'customer_id' => $customer?->id,
            ]);
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

            Log::info('Telegram customer lookup by phone completed.', [
                'phone_present' => true,
                'customer_found' => $customer !== null,
                'customer_id' => $customer?->id,
            ]);
        }

        if (! $customer) {
            $seedUsername = $telegramUsername !== ''
                ? $telegramUsername
                : ($telegramId !== '' ? 'tg_'.$telegramId : $name);

            Log::info('Telegram customer not found. Creating new customer.', [
                'seed_username' => $seedUsername,
            ]);

            $customer = new Customer([
                'username' => $this->generateUniqueUsername($seedUsername),
            ]);
        }

        if (! $customer->username) {
            $seedUsername = $telegramUsername !== ''
                ? $telegramUsername
                : ($telegramId !== '' ? 'tg_'.$telegramId : $name);

            Log::info('Telegram customer missing username. Generating one.', [
                'customer_id' => $customer->id,
                'seed_username' => $seedUsername,
            ]);

            $customer->username = $this->generateUniqueUsername($seedUsername);
        }

        $customer->name = $name;

        if ($phone !== '') {
            $customer->phone = $phone;
        }

        if ($telegramId !== '' && ($customer->telegram_id === null || $customer->telegram_id === $telegramId)) {
            $customer->telegram_id = $telegramId;
        }

        $isCreating = ! $customer->exists;

        Log::info('Telegram customer save initiated.', [
            'customer_id' => $customer->id,
            'is_creating' => $isCreating,
            'telegram_id' => $customer->telegram_id,
            'phone_present' => $customer->phone !== null && $customer->phone !== '',
            'username' => $customer->username,
        ]);

        $customer->save();

        Log::info('Telegram customer save completed.', [
            'customer_id' => $customer->id,
            'is_creating' => $isCreating,
            'telegram_id' => $customer->telegram_id,
            'phone_present' => $customer->phone !== null && $customer->phone !== '',
            'username' => $customer->username,
        ]);

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

        Log::info('Telegram username generation started.', [
            'seed' => $seed,
            'normalized' => $normalized,
            'base' => $base,
        ]);

        while (Customer::query()->where('username', $candidate)->exists()) {
            Log::info('Telegram username collision detected.', [
                'candidate' => $candidate,
                'counter' => $counter,
            ]);

            $suffix = '_'.$counter;
            $candidate = Str::limit($base, 24 - strlen($suffix), '').$suffix;
            $counter++;
        }

        Log::info('Telegram username generation completed.', [
            'seed' => $seed,
            'username' => $candidate,
        ]);

        return $candidate;
    }
}
