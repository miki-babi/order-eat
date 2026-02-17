<div
    class="menu-board"
    x-data="{
        selectedCategoryFilters: [],
        toggleCategoryFilter(id) {
            const value = String(id);

            if (this.selectedCategoryFilters.includes(value)) {
                this.selectedCategoryFilters = this.selectedCategoryFilters.filter((filter) => filter !== value);

                return;
            }

            this.selectedCategoryFilters = [...this.selectedCategoryFilters, value];
        },
        isCategoryFilterActive(id) {
            return this.selectedCategoryFilters.includes(String(id));
        },
        shouldShowCategory(id) {
            return this.selectedCategoryFilters.length === 0 || this.selectedCategoryFilters.includes(String(id));
        },
    }"
>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700&family=Manrope:wght@400;500;700&display=swap');

        .menu-board {
            --menu-bg: #f6efe2;
            --menu-ink: #1f1a16;
            --menu-muted: #6d5f52;
            --menu-card: #fffaf2;
            --menu-accent: #bc5f1f;
            --menu-accent-soft: #f1d8b7;
            --menu-line: #e8d7c0;
            --menu-shadow: 0 18px 45px -30px rgba(33, 22, 11, 0.55);
            color: var(--menu-ink);
            font-family: 'Manrope', 'Segoe UI', sans-serif;
        }

        .menu-shell {
            border-radius: 1.5rem;
            overflow: hidden;
            background:
                radial-gradient(circle at 10% 5%, #f7d9b5 0%, transparent 33%),
                radial-gradient(circle at 95% 10%, #f2d6be 0%, transparent 28%),
                linear-gradient(140deg, #f8efe0 0%, var(--menu-bg) 45%, #efe3d1 100%);
            box-shadow: var(--menu-shadow);
            border: 1px solid #ead8c0;
        }

        .menu-hero {
            position: relative;
            padding: 2.4rem clamp(1rem, 3vw, 2.8rem) 2rem;
            border-bottom: 1px solid var(--menu-line);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.48) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .menu-kicker {
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            color: #8d7357;
            margin-bottom: 0.65rem;
        }

        .menu-title {
            margin: 0;
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(1.9rem, 3.5vw, 3.1rem);
            line-height: 1.08;
            letter-spacing: 0.01em;
        }

        .menu-subtitle {
            margin: 0.8rem 0 0;
            max-width: 52ch;
            color: var(--menu-muted);
            font-size: 0.96rem;
        }

        .menu-stats {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            border: 1px solid #e5ccb1;
            background: rgba(255, 250, 243, 0.9);
            color: #7b5633;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .menu-category-jump {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 1.2rem;
        }

        .menu-jump-pill {
            text-decoration: none;
            color: #7b5530;
            border: 1px solid #e7ccb0;
            background: #fff8ee;
            border-radius: 999px;
            padding: 0.45rem 0.8rem;
            font-size: 0.78rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .menu-jump-pill:hover {
            color: #fff;
            border-color: var(--menu-accent);
            background: var(--menu-accent);
            transform: translateY(-1px);
        }

        .menu-jump-pill.is-active {
            color: #fff;
            border-color: var(--menu-accent);
            background: var(--menu-accent);
        }

        .menu-content {
            padding: clamp(1rem, 2.8vw, 2rem);
            display: grid;
            gap: 1.1rem;
        }

        .menu-category {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid #ead9c4;
            border-radius: 1.15rem;
            overflow: hidden;
            animation: menu-fade-up 0.48s ease both;
        }

        .menu-category:nth-child(2) { animation-delay: 0.06s; }
        .menu-category:nth-child(3) { animation-delay: 0.12s; }
        .menu-category:nth-child(4) { animation-delay: 0.18s; }
        .menu-category:nth-child(5) { animation-delay: 0.24s; }

        .menu-category-head {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr);
            padding: 1.05rem 1.15rem;
            border-bottom: 1px dashed #e5d1b8;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.56), rgba(255, 255, 255, 0));
        }

        .menu-category-title {
            margin: 0;
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(1.2rem, 2vw, 1.6rem);
            line-height: 1.12;
        }

        .menu-category-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .menu-category-description {
            margin: 0.5rem 0 0;
            color: var(--menu-muted);
            font-size: 0.9rem;
            max-width: 65ch;
        }

        .menu-category-image {
            width: 100%;
            max-width: 240px;
            aspect-ratio: 16/10;
            border-radius: 0.9rem;
            overflow: hidden;
            border: 1px solid #e8d4bc;
            box-shadow: 0 8px 26px -20px rgba(60, 34, 9, 0.7);
        }

        .menu-category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .menu-item-grid {
            padding: 0.95rem;
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .menu-item-card {
            border: 1px solid #ead8c3;
            background: var(--menu-card);
            border-radius: 0.95rem;
            overflow: hidden;
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            min-height: 92px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .menu-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -22px rgba(32, 20, 8, 0.7);
        }

        .menu-item-media {
            position: relative;
            background: linear-gradient(130deg, #f5debd 0%, #f1d0a6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-item-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .menu-item-fallback {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.75);
            color: #8d5e34;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .menu-item-body {
            padding: 0.72rem 0.8rem 0.78rem;
            display: grid;
            gap: 0.42rem;
        }

        .menu-item-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.6rem;
        }

        .menu-item-name {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .menu-item-price {
            margin: 0;
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1rem;
            color: var(--menu-accent);
            font-weight: 700;
            white-space: nowrap;
        }

        .menu-item-head-actions {
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .menu-edit-link {
            text-decoration: none;
            border: 1px solid #e9cfb2;
            background: #fff8ef;
            color: #8f5e33;
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .menu-edit-link:hover {
            color: #fff;
            border-color: var(--menu-accent);
            background: var(--menu-accent);
        }

        .menu-item-description {
            margin: 0;
            font-size: 0.82rem;
            color: #6f6052;
            line-height: 1.38;
        }

        .menu-item-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }

        .menu-tag {
            border: 1px solid #eccba8;
            border-radius: 999px;
            padding: 0.18rem 0.5rem;
            font-size: 0.67rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #9a6438;
            text-transform: uppercase;
            background: var(--menu-accent-soft);
        }

        .menu-empty {
            padding: 1.4rem;
            border-radius: 0.95rem;
            border: 1px dashed #d6c1a5;
            background: rgba(255, 255, 255, 0.58);
            color: #7d6b58;
            text-align: center;
            font-size: 0.95rem;
        }

        @keyframes menu-fade-up {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 768px) {
            .menu-category-head {
                grid-template-columns: minmax(0, 1fr) 240px;
                align-items: center;
            }

            .menu-item-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .menu-item-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>

    <div class="menu-shell">
        <section class="menu-hero">
            <p class="menu-kicker">House Specials</p>
            <h1 class="menu-title">Cafe Menu</h1>
            <p class="menu-subtitle">
                Freshly prepared items organized by category.
            </p>
            <div class="menu-stats">
                <span>{{ $categories->count() }} categories</span>
                <span>•</span>
                <span>{{ $categories->sum(fn ($category) => $category->items->count()) }} items</span>
            </div>

            @if ($categories->isNotEmpty())
                <div class="menu-category-jump">
                    @foreach ($categories as $category)
                        <button
                            type="button"
                            class="menu-jump-pill"
                            :class="{ 'is-active': isCategoryFilterActive('{{ (string) $category->id }}') }"
                            :aria-pressed="isCategoryFilterActive('{{ (string) $category->id }}')"
                            @click="toggleCategoryFilter('{{ (string) $category->id }}')"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="menu-content">
            @forelse ($categories as $category)
                <article
                    id="category-{{ $category->id }}"
                    class="menu-category"
                    x-show="shouldShowCategory('{{ (string) $category->id }}')"
                    x-transition.opacity.duration.200ms
                >
                    <header class="menu-category-head">
                        <div>
                            <div class="menu-category-title-row">
                                <h2 class="menu-category-title">{{ $category->name }}</h2>
                                <a href="{{ $this->getCategoryEditUrl($category) }}" class="menu-edit-link">
                                    Edit Category
                                </a>
                            </div>
                            @if (filled($category->description))
                                <p class="menu-category-description">{{ $category->description }}</p>
                            @endif
                        </div>

                        @if ($categoryImageUrl = $this->getCategoryImageUrl($category->image_path))
                            <div class="menu-category-image">
                                <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}">
                            </div>
                        @endif
                    </header>

                    <div class="menu-item-grid">
                        @forelse ($category->items as $item)
                            <article class="menu-item-card">
                                <div class="menu-item-media">
                                    @if ($itemImageUrl = $this->getItemPrimaryImageUrl($item))
                                        <img src="{{ $itemImageUrl }}" alt="{{ $item->name }}">
                                    @else
                                        <span class="menu-item-fallback">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($item->name, 0, 1)) }}</span>
                                    @endif
                                </div>

                                <div class="menu-item-body">
                                    <div class="menu-item-head">
                                        <h3 class="menu-item-name">{{ $item->name }}</h3>
                                        <div class="menu-item-head-actions">
                                            <p class="menu-item-price">${{ number_format((float) $item->price, 2) }}</p>
                                            <a href="{{ $this->getItemEditUrl($item) }}" class="menu-edit-link">
                                                Edit
                                            </a>
                                        </div>
                                    </div>

                                    <p class="menu-item-description">
                                        {{ $item->description ?: 'No description added yet.' }}
                                    </p>

                                    <div class="menu-item-tags">
                                        @if ($item->is_featured)
                                            <span class="menu-tag">Featured</span>
                                        @endif

                                        @if ($item->itemImages->isNotEmpty())
                                            <span class="menu-tag">{{ $item->itemImages->count() }} image{{ $item->itemImages->count() > 1 ? 's' : '' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="menu-empty">
                                No active items in this category yet.
                            </p>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="menu-empty">
                    No active categories found. Add categories and items to render the menu.
                </div>
            @endforelse
        </section>
    </div>
</div>
