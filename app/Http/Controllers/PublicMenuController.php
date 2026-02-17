<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PublicMenuController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('items', fn ($query) => $query->where('is_active', true))
            ->with([
                'items' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->with([
                        'itemImages' => fn ($imageQuery) => $imageQuery
                            ->orderByDesc('is_primary')
                            ->orderBy('id'),
                    ]),
            ])
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $branches = Branch::query()
            ->orderBy('name')
            ->get(['id', 'name', 'address']);

        return view('welcome', [
            'categories' => $categories,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'delivery_method' => ['required', Rule::in(['self-pickup', 'delivery'])],
            'payment_method' => ['nullable', Rule::in(['cash', 'transfer'])],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
            'telegram_init_data' => ['nullable', 'string'],
            'telegram_init_data_unsafe' => ['nullable', 'string'],
            'telegram_query_id' => ['nullable', 'string', 'max:255'],
            'telegram_auth_date' => ['nullable', 'string', 'max:255'],
            'telegram_start_param' => ['nullable', 'string', 'max:255'],
            'telegram_user_id' => ['nullable', 'string', 'max:255'],
            'telegram_username' => ['nullable', 'string', 'max:255'],
            'telegram_first_name' => ['nullable', 'string', 'max:255'],
            'telegram_last_name' => ['nullable', 'string', 'max:255'],
            'telegram_language_code' => ['nullable', 'string', 'max:40'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'telegram_chat_type' => ['nullable', 'string', 'max:255'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);

        $quantities = collect($validated['quantities'])
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($quantities->isEmpty()) {
            return back()
                ->withErrors(['quantities' => 'Please add at least one menu item.'])
                ->withInput();
        }

        $items = Item::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->whereIn('id', $quantities->keys())
            ->get(['id', 'price']);

        if ($items->count() !== $quantities->count()) {
            return back()
                ->withErrors(['quantities' => 'One or more selected items are unavailable.'])
                ->withInput();
        }

        $totalPrice = $this->calculateTotal($items, $quantities);

        if ($totalPrice <= 0) {
            return back()
                ->withErrors(['quantities' => 'Unable to create an order with zero total.'])
                ->withInput();
        }

        $telegramUserId = trim((string) ($validated['telegram_user_id'] ?? ''));
        $telegramUsername = trim((string) ($validated['telegram_username'] ?? ''));

        $order = DB::transaction(function () use (
            $validated,
            $quantities,
            $items,
            $totalPrice,
            $telegramUserId,
            $telegramUsername
        ): Order {
            $customer = $this->resolveCustomer(
                name: $validated['name'],
                requestedUsername: $validated['username'] ?? null,
                phone: $validated['phone'] ?? null,
                telegramId: $telegramUserId !== '' ? $telegramUserId : null,
                telegramUsername: $telegramUsername !== '' ? $telegramUsername : null,
            );

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'branch_id' => $validated['branch_id'],
                'status' => 'pending',
                'total_price' => round($totalPrice, 2),
                'special_instructions' => $validated['special_instructions'] ?: null,
                'payment_method' => $validated['payment_method'] ?: null,
                'payment_status' => 'pending',
                'delivery_method' => $validated['delivery_method'],
                'order_time' => now(),
            ]);

            $now = now();
            $orderItemsPayload = [];

            foreach ($items as $item) {
                $quantity = (int) $quantities->get($item->id, 0);

                for ($index = 0; $index < $quantity; $index++) {
                    $orderItemsPayload[] = [
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            OrderItem::query()->insert($orderItemsPayload);

            return $order;
        });

        return redirect()
            ->route('menu.index')
            ->with('success', "Order #{$order->id} has been placed.");
    }

    private function calculateTotal(Collection $items, Collection $quantities): float
    {
        return (float) $items->sum(function (Item $item) use ($quantities): float {
            $quantity = (int) $quantities->get($item->id, 0);

            return ((float) $item->price) * $quantity;
        });
    }

    private function resolveCustomer(
        string $name,
        ?string $requestedUsername,
        ?string $phone,
        ?string $telegramId,
        ?string $telegramUsername,
    ): Customer {
        $requestedUsername = trim((string) $requestedUsername);
        $telegramUsername = trim((string) $telegramUsername);
        $phone = trim((string) $phone);

        $customer = null;

        if ($telegramId !== null && $telegramId !== '') {
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

                    if ($telegramId !== null && $telegramId !== '') {
                        $query->orWhere('telegram_id', $telegramId);
                    }
                })
                ->latest('id')
                ->first();
        }

        if (! $customer) {
            $seedUsername = $requestedUsername !== ''
                ? $requestedUsername
                : ($telegramUsername !== '' ? $telegramUsername : ($telegramId ? 'tg_'.$telegramId : $name));

            $customer = new Customer([
                'username' => $this->generateUniqueUsername($seedUsername),
            ]);
        }

        if (! $customer->username) {
            $seedUsername = $requestedUsername !== ''
                ? $requestedUsername
                : ($telegramUsername !== '' ? $telegramUsername : ($telegramId ? 'tg_'.$telegramId : $name));

            $customer->username = $this->generateUniqueUsername($seedUsername);
        }

        $customer->name = $name;

        if ($phone !== '') {
            $customer->phone = $phone;
        }

        if ($telegramId !== null && $telegramId !== '' && ($customer->telegram_id === null || $customer->telegram_id === $telegramId)) {
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
            $suffix = '_' . $counter;
            $candidate = Str::limit($base, 24 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $candidate;
    }
}
