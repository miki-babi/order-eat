<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digital Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body>
    @php
        $oldQuantities = collect(old('quantities', []))->map(fn($value) => max(0, (int) $value));
        $flatItems = $categories->flatMap(fn($category) => $category->items)->values();
        $oldItemCount = $oldQuantities->sum();
        $oldSubtotal = $flatItems->sum(fn($item) => ((float) $item->price) * (int) $oldQuantities->get($item->id, 0));
        $selectedBranchId = (string) old('branch_id', $branches->first()?->id);

        $featuredItems = $flatItems->where('is_featured', true)->take(10);

        if ($featuredItems->isEmpty()) {
            $featuredItems = $flatItems->take(10);
        }
    @endphp

    <div class="">
        <main class="main-content">
            <div class="app-container">
                @if (session('success'))
                    <div id="session-success-trigger" data-message="{{ session('success') }}" hidden></div>
                @endif

                @if ($errors->any())
                    <div id="session-error-trigger"
                        data-message="Please review your details and confirm the order again." hidden></div>
                @endif

                <header class="hero">
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                        <span class="hero-badge">
                            <span class="hero-badge-mark">M</span>
                            <span>Digital Menu</span>
                        </span>
                        <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                            <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="display: none; width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.364l-.707.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="display: none; width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                    <h1>Hey,<br>what's up?</h1>
                    {{-- <p>Pick from featured items, browse categories, then place your order in seconds.</p> --}}
                </header>

                @if ($featuredItems->isNotEmpty())
                    <section class="section">
                        <div class="section-head">
                            <h2>Featured</h2>
                            <p>Popular right now</p>
                        </div>

                        <div class="featured-track" id="featured-track">
                            @foreach ($featuredItems as $item)
                                @php
                                    $featuredImage =
                                        $item->itemImages->firstWhere('is_primary', true)?->image_path ??
                                        $item->itemImages->first()?->image_path;
                                    $oldQuantity = (int) old("quantities.{$item->id}", 0);
                                @endphp

                                <article class="featured-card" data-item-card data-item-id="{{ $item->id }}"
                                    data-item-name="{{ e($item->name) }}"
                                    data-item-image="{{ $featuredImage ? asset('storage/' . $featuredImage) : '' }}"
                                    data-item-price="{{ number_format((float) $item->price, 2, '.', '') }}">
                                    <span class="featured-badge">{{ $loop->odd ? 'Popular' : "Chef's Choice" }}</span>

                                    <div class="featured-media">
                                        @if ($featuredImage)
                                            <img src="{{ asset('storage/' . $featuredImage) }}"
                                                alt="{{ $item->name }}" loading="lazy">
                                        @else
                                            <span class="media-fallback">No image</span>
                                        @endif
                                    </div>

                                    <div class="featured-content">
                                        <h3 class="featured-title">{{ $item->name }}</h3>

                                        <div class="featured-foot">
                                            <p class="featured-price">${{ number_format((float) $item->price, 2) }}</p>
                                            <button type="button" class="add-btn" data-add-btn
                                                data-qty-action="increase" data-item-id="{{ $item->id }}"
                                                aria-label="Add {{ $item->name }}">
                                                + Add
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($categories->isNotEmpty())
                    <section class="section">
                        <div class="category-tabs-wrap">
                            <nav class="category-tabs" aria-label="Menu categories">
                                @foreach ($categories as $category)
                                    <a href="#category-{{ $category->id }}" class="category-tab" data-category-tab
                                        data-category-id="category-{{ $category->id }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        @foreach ($categories as $category)
                            <section class="menu-section" id="category-{{ $category->id }}" data-category-section>
                                <h3>{{ $category->name }}</h3>
                                @if ($category->description)
                                    <p>{{ $category->description }}</p>
                                @endif

                                <div class="items-grid">
                                    @foreach ($category->items as $item)
                                        @php
                                            $primaryImage =
                                                $item->itemImages->firstWhere('is_primary', true) ??
                                                $item->itemImages->first();
                                            $oldQuantity = (int) old("quantities.{$item->id}", 0);
                                        @endphp

                                        <article class="item-card" data-item-card data-item-id="{{ $item->id }}"
                                            data-item-name="{{ e($item->name) }}"
                                            data-item-image="{{ $primaryImage?->image_path ? asset('storage/' . $primaryImage->image_path) : '' }}"
                                            data-item-price="{{ number_format((float) $item->price, 2, '.', '') }}">
                                            <div class="item-media">
                                                @if ($primaryImage?->image_path)
                                                    <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                                        alt="{{ $item->name }}" loading="lazy">
                                                @else
                                                    <span class="media-fallback">No image</span>
                                                @endif
                                            </div>

                                            <div>
                                                <h4 class="item-name">{{ $item->name }}</h4>
                                                <p class="item-desc">
                                                    {{ $item->description ?: 'Freshly prepared and ready to order.' }}
                                                </p>
                                            </div>

                                            <div class="item-foot">
                                                <p class="item-price">${{ number_format((float) $item->price, 2) }}
                                                </p>
                                                <div class="qty-inline">
                                                    <button type="button" class="qty-btn" data-qty-action="decrease"
                                                        data-item-id="{{ $item->id }}"
                                                        aria-label="Decrease {{ $item->name }}">
                                                        -
                                                    </button>
                                                    <span class="qty-value"
                                                        data-qty-label="{{ $item->id }}">{{ $oldQuantity }}</span>
                                                    <button type="button" class="add-btn" data-add-btn
                                                        data-qty-action="increase" data-item-id="{{ $item->id }}"
                                                        aria-label="Add {{ $item->name }}">
                                                        + Add
                                                    </button>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </section>
                @else
                    <div class="empty-state">No active menu items are available yet.</div>
                @endif
            </div> <!-- app-container -->
        </main>

        <aside class="side-panel">
            <div class="drawer-head"
                style="padding: 0 0 1.5rem 0; border-bottom: 2px solid var(--line); margin-bottom: 2rem;">
                <h2 style="margin: 0; font-family: 'Sora', sans-serif; font-size: 1.75rem; font-weight: 800;">Your
                    Order
                </h2>
            </div>

            <div id="side-summary-content">
                <div class="order-summary" style="border: none; padding: 0; background: none; margin-top: 0;">
                    <div class="summary-list" id="side-summary-list"
                        style="max-height: none; gap: 1.25rem; display: flex; flex-direction: column;">
                        <p class="summary-empty"
                            style="padding: 3rem 0; text-align: center; color: var(--muted); font-size: 1rem;">Your
                            cart
                            is empty</p>
                    </div>

                    <div class="summary-total"
                        style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--line); display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 1.125rem; color: var(--muted); font-weight: 600;">Subtotal</span>
                        <strong data-subtotal
                            style="font-size: 2.5rem; font-family: 'Sora', sans-serif; letter-spacing: -0.04em;">$0.00</strong>
                    </div>
                </div>

                <button type="button" class="confirm-btn" id="side-checkout-trigger"
                    style="margin-top: 2.5rem; padding: 1.25rem; width: 100%; border-radius: var(--radius-md); background: var(--yellow); font-weight: 900; font-size: 1.25rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 6px 0 var(--yellow-strong); transform: translateY(-4px);">
                    Checkout Now
                </button>
                <div
                    style="margin-top: 2rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; color: #059669; font-weight: 700; font-size: 0.875rem; background: #ecfdf5; padding: 0.75rem; border-radius: var(--radius-sm);">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Secure Order
                </div>
            </div>
        </aside>
    </div> <!-- layout-wrapper -->

    <div class="floating-order-bar {{ $oldItemCount > 0 ? 'is-visible' : '' }}" id="floating-order-bar">
        <button type="button" class="floating-order-trigger" id="open-order-drawer" aria-controls="order-drawer"
            aria-expanded="false">
            <span class="floating-meta">
                <strong><span class="floating-value" data-total-count>{{ $oldItemCount }}</span> items</strong>
                <span class="floating-value" data-subtotal>${{ number_format((float) $oldSubtotal, 2) }}</span>
            </span>
            <span class="floating-cta">Place Order</span>
        </button>
    </div>

    <div class="drawer-overlay {{ $errors->any() ? 'is-open' : '' }}" id="drawer-overlay"></div>

    <aside class="order-drawer {{ $errors->any() ? 'is-open' : '' }}" id="order-drawer"
        aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
        <header class="drawer-head">
            <div>
                <h2>Review Order</h2>
                <p>Confirm items, add details, and place order.</p>
            </div>
            <button type="button" class="drawer-close" id="close-order-drawer"
                aria-label="Close order drawer">✕</button>
        </header>

        <div class="drawer-body">
            <form id="order-form" action="{{ route('menu.order.store') }}" method="POST">
                @csrf

                <div aria-hidden="true" hidden>
                    @foreach ($categories as $category)
                        @foreach ($category->items as $item)
                            <input type="hidden" name="quantities[{{ $item->id }}]"
                                value="{{ max(0, (int) old("quantities.{$item->id}", 0)) }}"
                                data-qty-input="{{ $item->id }}">
                        @endforeach
                    @endforeach

                    <input type="hidden" name="telegram_init_data" value="{{ old('telegram_init_data', '') }}"
                        data-tg-field="telegram_init_data">
                    <input type="hidden" name="telegram_init_data_unsafe"
                        value="{{ old('telegram_init_data_unsafe', '') }}" data-tg-field="telegram_init_data_unsafe">
                    <input type="hidden" name="telegram_query_id" value="{{ old('telegram_query_id', '') }}"
                        data-tg-field="telegram_query_id">
                    <input type="hidden" name="telegram_auth_date" value="{{ old('telegram_auth_date', '') }}"
                        data-tg-field="telegram_auth_date">
                    <input type="hidden" name="telegram_start_param" value="{{ old('telegram_start_param', '') }}"
                        data-tg-field="telegram_start_param">
                    <input type="hidden" name="telegram_user_id" value="{{ old('telegram_user_id', '') }}"
                        data-tg-field="telegram_user_id">
                    <input type="hidden" name="telegram_username" value="{{ old('telegram_username', '') }}"
                        data-tg-field="telegram_username">
                    <input type="hidden" name="telegram_first_name" value="{{ old('telegram_first_name', '') }}"
                        data-tg-field="telegram_first_name">
                    <input type="hidden" name="telegram_last_name" value="{{ old('telegram_last_name', '') }}"
                        data-tg-field="telegram_last_name">
                    <input type="hidden" name="telegram_language_code"
                        value="{{ old('telegram_language_code', '') }}" data-tg-field="telegram_language_code">
                    <input type="hidden" name="telegram_chat_id" value="{{ old('telegram_chat_id', '') }}"
                        data-tg-field="telegram_chat_id">
                    <input type="hidden" name="telegram_chat_type" value="{{ old('telegram_chat_type', '') }}"
                        data-tg-field="telegram_chat_type">
                </div>

                <section class="order-summary" aria-live="polite">
                    <div class="order-summary-head">
                        <h3>Items</h3>
                        <span><span data-total-count>{{ $oldItemCount }}</span> selected</span>
                    </div>

                    <div class="summary-list" id="order-summary-list">
                        <div class="summary-empty" id="order-summary-empty"
                            style="text-align: center; padding: 2rem 0;">
                            <p style="margin: 0; color: var(--muted);">No items selected yet.</p>
                            <button type="button" class="back-to-menu-btn" onclick="closeDrawer()">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Add Items
                            </button>
                        </div>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong data-subtotal>${{ number_format((float) $oldSubtotal, 2) }}</strong>
                    </div>
                </section>

                <div class="field-grid" style="margin-top: 0.72rem;">
                    <label class="field">
                        Full Name
                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="255">
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    {{-- <label class="field">
                        Username (Optional)
                        <input type="text" name="username" value="{{ old('username') }}" maxlength="255"
                            placeholder="Auto-generated if empty">
                        @error('username')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label> --}}

                    <label class="field">
                        Phone Number
                        <input type="tel" name="phone" value="{{ old('phone') }}" required maxlength="40"
                            placeholder="e.g. +1 234 567 890">
                        @error('phone')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Branch
                        <select name="branch_id" required>
                            <option value="">Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === (string) $branch->id)>
                                    {{ $branch->name }}@if ($branch->address)
                                        - {{ $branch->address }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Delivery Method
                        <select name="delivery_method" required>
                            <option value="self-pickup" @selected(old('delivery_method', 'self-pickup') === 'self-pickup')>Self Pickup</option>
                            <option value="delivery" @selected(old('delivery_method') === 'delivery')>Delivery</option>
                        </select>
                        @error('delivery_method')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Payment Method
                        <select name="payment_method">
                            <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                            <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                        </select>
                        @error('payment_method')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Special Instructions
                        <textarea name="special_instructions" maxlength="1000">{{ old('special_instructions') }}</textarea>
                        @error('special_instructions')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>
                </div>

                @error('quantities')
                    <p class="field-error" style="margin-top: 0.5rem;">{{ $message }}</p>
                @enderror

                <button type="submit" class="confirm-btn" data-submit-order
                    data-no-branches="{{ $branches->isEmpty() ? 1 : 0 }}" @disabled($branches->isEmpty() || $oldItemCount === 0)>
                    Confirm Order
                </button>

                @if ($branches->isEmpty())
                    <p class="helper-note">No branch is available yet, so ordering is disabled.</p>
                @else
                    <p class="helper-note">Telegram ID is intentionally skipped for now.</p>
                @endif
            </form>
        </div>
    </aside>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmation-modal">
        <article class="modal-card">
            <div id="modal-icon" class="modal-icon success">✓</div>
            <h2 id="modal-title" class="modal-title">Success!</h2>
            <p id="modal-desc" class="modal-desc">Your order has been placed successfully.</p>
            <button type="button" class="modal-btn" onclick="closeModal()">Got it!</button>
        </article>
    </div>

    <script>
        (() => {
            const featuredTrack = document.getElementById('featured-track');
            const featuredCards = Array.from(document.querySelectorAll('.featured-card'));
            const categoryTabs = Array.from(document.querySelectorAll('[data-category-tab]'));
            const categorySections = Array.from(document.querySelectorAll('[data-category-section]'));

            const qtyInputs = Array.from(document.querySelectorAll('[data-qty-input]'));
            const telegramFieldInputs = Array.from(document.querySelectorAll('[data-tg-field]'));
            const summaryList = document.getElementById('order-summary-list');
            const emptyRow = document.getElementById('order-summary-empty');
            const countEls = Array.from(document.querySelectorAll('[data-total-count]'));
            const subtotalEls = Array.from(document.querySelectorAll('[data-subtotal]'));

            const floatingBar = document.getElementById('floating-order-bar');
            const openDrawerButton = document.getElementById('open-order-drawer');
            const closeDrawerButton = document.getElementById('close-order-drawer');
            const drawerOverlay = document.getElementById('drawer-overlay');
            const orderDrawer = document.getElementById('order-drawer');
            const orderForm = document.getElementById('order-form');

            const submitButton = document.querySelector('[data-submit-order]');
            const hasNoBranches = submitButton?.dataset.noBranches === '1';
            const currency = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            });
            const shouldStartOpen = @json($errors->any());
            const telegramPrefillEndpoint = @json(route('telegram.customer-prefill'));
            const nameInput = document.querySelector('input[name="name"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            const usernameInput = document.querySelector('input[name="username"]');

            // Modal Elements
            const modalOverlay = document.getElementById('confirmation-modal');
            const modalIcon = document.getElementById('modal-icon');
            const modalTitle = document.getElementById('modal-title');
            const modalDesc = document.getElementById('modal-desc');

            window.showModal = (title, message, type = 'success') => {
                modalTitle.textContent = title;
                modalDesc.textContent = message;
                modalIcon.textContent = type === 'success' ? '✓' : '!';
                modalIcon.className = `modal-icon ${type}`;
                modalOverlay.classList.add('is-active');
            };

            window.closeModal = () => {
                modalOverlay.classList.remove('is-active');
            };

            // Check for session triggers
            const successTrigger = document.getElementById('session-success-trigger');
            const errorTrigger = document.getElementById('session-error-trigger');

            if (successTrigger) {
                showModal('Order Placed!', successTrigger.dataset.message, 'success');
            } else if (errorTrigger) {
                showModal('Oops!', errorTrigger.dataset.message, 'error');
            }

            const itemMeta = new Map();
            const itemCards = Array.from(document.querySelectorAll('[data-item-card]'));
            const telegramFieldMap = new Map(
                telegramFieldInputs.map((input) => [input.dataset.tgField, input])
            );

            itemCards.forEach((card) => {
                const itemId = card.dataset.itemId;

                if (!itemId || itemMeta.has(itemId)) {
                    return;
                }

                itemMeta.set(itemId, {
                    id: itemId,
                    name: card.dataset.itemName ?? '',
                    image: card.dataset.itemImage ?? '',
                    price: Number.parseFloat(card.dataset.itemPrice ?? '0') || 0,
                });
            });

            let previousCount = null;
            let previousSubtotal = null;
            let customerPrefillRequested = false;

            const clampQty = (value) => {
                const number = Number.parseInt(value, 10);

                if (Number.isNaN(number) || number < 0) {
                    return 0;
                }

                if (number > 20) {
                    return 20;
                }

                return number;
            };

            const getInputForItem = (itemId) => document.querySelector(`[data-qty-input="${itemId}"]`);
            const getLabelsForItem = (itemId) => Array.from(document.querySelectorAll(`[data-qty-label="${itemId}"]`));
            const setTelegramField = (key, value) => {
                const field = telegramFieldMap.get(key);

                if (!field || value === null || value === undefined || value === '') {
                    return;
                }

                field.value = String(value);
            };
            const setInputIfEmpty = (input, value) => {
                if (!input || value === null || value === undefined) {
                    return;
                }

                if (input.value.trim() !== '') {
                    return;
                }

                const nextValue = String(value).trim();

                if (nextValue === '') {
                    return;
                }

                input.value = nextValue;
            };
            const requestCustomerPrefill = async (telegramUserId) => {
                if (customerPrefillRequested || !telegramUserId) {
                    return;
                }

                customerPrefillRequested = true;

                try {
                    const response = await fetch(
                        `${telegramPrefillEndpoint}?telegram_user_id=${encodeURIComponent(String(telegramUserId))}`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        }
                    );

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const customer = payload?.customer;

                    if (!customer) {
                        return;
                    }

                    setInputIfEmpty(nameInput, customer.name);
                    setInputIfEmpty(phoneInput, customer.phone);
                    setInputIfEmpty(usernameInput, customer.username);
                } catch (error) {
                    // Skip prefill silently when lookup fails.
                }
            };

            const pulseElement = (element) => {
                element.classList.remove('bump');
                void element.offsetWidth;
                element.classList.add('bump');
            };

            const captureTelegramInitData = () => {
                const webApp = window.Telegram?.WebApp;

                if (!webApp) {
                    return;
                }

                const unsafe = webApp.initDataUnsafe ?? {};
                const user = unsafe.user ?? {};
                const chat = unsafe.chat ?? {};

                setTelegramField('telegram_init_data', webApp.initData ?? '');
                setTelegramField('telegram_query_id', unsafe.query_id);
                setTelegramField('telegram_auth_date', unsafe.auth_date);
                setTelegramField('telegram_start_param', unsafe.start_param);

                setTelegramField('telegram_user_id', user.id);
                setTelegramField('telegram_username', user.username);
                setTelegramField('telegram_first_name', user.first_name);
                setTelegramField('telegram_last_name', user.last_name);
                setTelegramField('telegram_language_code', user.language_code);

                setTelegramField('telegram_chat_id', chat.id);
                setTelegramField('telegram_chat_type', chat.type);

                try {
                    if (Object.keys(unsafe).length > 0) {
                        setTelegramField('telegram_init_data_unsafe', JSON.stringify(unsafe));
                    }
                } catch (error) {
                    // Keep raw init data even if unsafe payload cannot be serialized.
                }

                const fullName = [user.first_name, user.last_name]
                    .filter((part) => typeof part === 'string' && part.trim() !== '')
                    .join(' ')
                    .trim();

                setInputIfEmpty(nameInput, fullName);
                setInputIfEmpty(usernameInput, user.username);

                if (user.id !== null && user.id !== undefined && user.id !== '') {
                    requestCustomerPrefill(user.id);
                }
            };

            const setQty = (itemId, quantity) => {
                const input = getInputForItem(itemId);

                if (!input) {
                    return;
                }

                input.value = String(clampQty(quantity));
                getLabelsForItem(itemId).forEach((label) => pulseElement(label));
            };

            const openDrawer = () => {
                orderDrawer.classList.add('is-open');
                drawerOverlay.classList.add('is-open');
                document.body.classList.add('drawer-open');
                orderDrawer.setAttribute('aria-hidden', 'false');
                openDrawerButton?.setAttribute('aria-expanded', 'true');

                // Hide floating bar when drawer opens
                floatingBar.classList.remove('is-visible');
            };

            const closeDrawer = () => {
                orderDrawer.classList.remove('is-open');
                drawerOverlay.classList.remove('is-open');
                document.body.classList.remove('drawer-open');
                orderDrawer.setAttribute('aria-hidden', 'true');
                openDrawerButton?.setAttribute('aria-expanded', 'false');

                // Re-evaluate floating bar visibility
                renderSummary();
            };

            // Expose globally for the onclick handler
            window.closeDrawer = closeDrawer;

            const sideSummaryList = document.getElementById('side-summary-list');
            const sideSummaryEmpty = sideSummaryList?.querySelector('.summary-empty');
            const sideCheckoutTrigger = document.getElementById('side-checkout-trigger');

            const renderSummary = () => {
                let selectedCount = 0;
                let subtotal = 0;

                summaryList.querySelectorAll('.summary-line').forEach((line) => line.remove());
                if (sideSummaryList) {
                    sideSummaryList.querySelectorAll('.summary-line:not(.summary-empty)').forEach((line) => line
                        .remove());
                }

                qtyInputs.forEach((input) => {
                    const itemId = input.dataset.qtyInput;
                    const quantity = clampQty(input.value);
                    const meta = itemMeta.get(itemId);

                    getLabelsForItem(itemId).forEach((label) => {
                        label.textContent = String(quantity);
                    });

                    if (!meta || quantity < 1) {
                        return;
                    }

                    selectedCount += quantity;
                    const lineTotal = meta.price * quantity;
                    subtotal += lineTotal;

                    // Drawer Summary
                    const row = document.createElement('div');
                    row.className = 'summary-line';
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.gap = '1rem';
                    row.style.padding = '0.75rem 0';

                    const imgHtml = meta.image ?
                        `<img src="${meta.image}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 12px; border: 1px solid var(--line);">` :
                        `<div style="width: 48px; height: 48px; background: var(--surface-soft); border-radius: 12px; border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; color: var(--muted);">No img</div>`;

                    row.innerHTML = `
                        ${imgHtml}
                        <div style="flex-grow: 1;">
                            <span style="display: block; font-weight: 700;">${meta.name}</span>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="decrease">−</button>
                                <span style="font-weight: 800; font-size: 0.9rem; min-width: 20px; text-align: center;">${quantity}</span>
                                <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="increase">+</button>
                            </div>
                        </div>
                        <strong style="font-size: 1rem;">${currency.format(lineTotal)}</strong>
                    `;
                    summaryList.appendChild(row);

                    // Side Summary
                    if (sideSummaryList) {
                        const sideRow = document.createElement('div');
                        sideRow.className = 'summary-line';
                        sideRow.style.display = 'flex';
                        sideRow.style.justifyContent = 'space-between';
                        sideRow.style.alignItems = 'center';
                        sideRow.style.padding = '0.5rem 0';
                        sideRow.style.gap = '1rem';

                        sideRow.innerHTML = `
                            ${imgHtml}
                            <div style="flex-grow: 1; display: flex; flex-direction: column;">
                                <span style="font-weight: 700; font-size: 1rem;">${meta.name}</span>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                    <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="decrease">−</button>
                                    <span style="font-weight: 800; font-size: 0.9rem; min-width: 20px; text-align: center;">${quantity}</span>
                                    <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="increase">+</button>
                                </div>
                            </div>
                            <strong style="font-size: 1.125rem; font-weight: 800;">${currency.format(lineTotal)}</strong>
                        `;
                        sideSummaryList.appendChild(sideRow);
                    }
                });

                if (emptyRow) {
                    emptyRow.style.display = selectedCount > 0 ? 'none' : 'block';
                }
                if (sideSummaryEmpty) {
                    sideSummaryEmpty.style.display = selectedCount > 0 ? 'none' : 'block';
                }

                countEls.forEach((element) => {
                    element.textContent = String(selectedCount);
                    if (previousCount !== null && previousCount !== selectedCount) {
                        pulseElement(element);
                    }
                });

                subtotalEls.forEach((element) => {
                    element.textContent = currency.format(subtotal);
                    if (previousSubtotal !== null && previousSubtotal !== subtotal) {
                        pulseElement(element);
                    }
                });

                const isDrawerOpen = orderDrawer.classList.contains('is-open');
                floatingBar.classList.toggle('is-visible', selectedCount > 0 && !isDrawerOpen);

                if (submitButton) {
                    submitButton.disabled = selectedCount === 0 || hasNoBranches;
                }

                if (sideCheckoutTrigger) {
                    sideCheckoutTrigger.disabled = selectedCount === 0 || hasNoBranches;
                    sideCheckoutTrigger.style.opacity = (selectedCount === 0 || hasNoBranches) ? '0.5' : '1';
                }

                previousCount = selectedCount;
                previousSubtotal = subtotal;
            };

            sideCheckoutTrigger?.addEventListener('click', openDrawer);

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-qty-action]');

                if (!button) {
                    return;
                }

                const itemId = button.dataset.itemId;
                const input = getInputForItem(itemId);

                if (!input) {
                    return;
                }

                const current = clampQty(input.value);
                const isIncrease = button.dataset.qtyAction === 'increase';
                const next = isIncrease ? current + 1 : current - 1;
                setQty(itemId, next);

                if (isIncrease) {
                    const cards = document.querySelectorAll(`[data-item-id="${itemId}"]`);
                    cards.forEach(card => {
                        card.classList.remove('is-popping');
                        void card.offsetWidth;
                        card.classList.add('is-popping');
                        setTimeout(() => card.classList.remove('is-popping'), 400);
                    });
                }

                if (button.matches('[data-add-btn]')) {
                    pulseElement(button);
                }

                renderSummary();
            });

            openDrawerButton?.addEventListener('click', openDrawer);
            closeDrawerButton?.addEventListener('click', closeDrawer);
            drawerOverlay?.addEventListener('click', closeDrawer);
            orderForm?.addEventListener('submit', () => captureTelegramInitData());

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDrawer();
                }
            });

            categoryTabs.forEach((tab) => {
                tab.addEventListener('click', (event) => {
                    event.preventDefault();

                    const targetId = tab.dataset.categoryId;
                    const target = targetId ? document.getElementById(targetId) : null;

                    if (!target) {
                        return;
                    }

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            if (categoryTabs[0]) {
                categoryTabs[0].classList.add('is-active');
            }

            // Theme Toggle Logic
            const themeToggle = document.getElementById('theme-toggle');
            const getTheme = () => document.documentElement.getAttribute('data-theme');
            const setTheme = (theme) => {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            };

            themeToggle?.addEventListener('click', () => {
                const nextTheme = getTheme() === 'dark' ? 'light' : 'dark';
                setTheme(nextTheme);
            });

            // Auto-hide flash messages
            const flash = document.querySelector('.flash');
            if (flash) {
                setTimeout(() => {
                    flash.style.transition = 'all 0.4s cubic-bezier(0.2, 0, 0, 1)';
                    flash.style.transform = 'translate(-50%, -100%)';
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 400);
                }, 4000);
            }

            if (window.IntersectionObserver) {
                if (featuredTrack && featuredCards.length > 0) {
                    const featuredObserver = new IntersectionObserver(
                        (entries) => {
                            entries.forEach((entry) => {
                                entry.target.classList.toggle('is-active', entry.intersectionRatio >= 0.65);
                            });
                        }, {
                            root: featuredTrack,
                            threshold: [0.45, 0.65, 0.85],
                        }
                    );

                    featuredCards.forEach((card) => featuredObserver.observe(card));
                }

                if (categorySections.length > 0 && categoryTabs.length > 0) {
                    const tabMap = new Map(
                        categoryTabs.map((tab) => [tab.dataset.categoryId, tab])
                    );

                    const activateTab = (sectionId) => {
                        categoryTabs.forEach((tab) => tab.classList.remove('is-active'));
                        tabMap.get(sectionId)?.classList.add('is-active');
                    };

                    const sectionObserver = new IntersectionObserver(
                        (entries) => {
                            const visible = entries
                                .filter((entry) => entry.isIntersecting)
                                .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

                            if (visible[0]?.target?.id) {
                                activateTab(visible[0].target.id);
                            }
                        }, {
                            root: null,
                            threshold: [0.2, 0.45, 0.7],
                            rootMargin: '-25% 0px -60% 0px',
                        }
                    );

                    categorySections.forEach((section) => sectionObserver.observe(section));

                    if (categorySections[0]?.id) {
                        activateTab(categorySections[0].id);
                    }
                }
            }

            captureTelegramInitData();
            renderSummary();

            if (shouldStartOpen) {
                openDrawer();
            }
        })();
    </script>
</body>

</html>
