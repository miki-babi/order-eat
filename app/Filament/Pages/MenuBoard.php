<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Items\ItemResource;
use App\Models\Category;
use App\Models\Item;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MenuBoard extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $navigationLabel = 'Menu';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'menu';

    protected static ?string $title = 'Cafe Menu';

    protected string $view = 'filament.pages.menu-board';

    protected Width | string | null $maxContentWidth = Width::Full;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
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
            ->get();

        return [
            'categories' => $categories,
        ];
    }

    public function getCategoryImageUrl(?string $path): ?string
    {
        return $this->resolveImageUrl($path);
    }

    public function getItemPrimaryImageUrl(Item $item): ?string
    {
        $primaryImage = $item->itemImages->firstWhere('is_primary', true) ?? $item->itemImages->first();

        return $this->resolveImageUrl($primaryImage?->image_path);
    }

    public function getCategoryEditUrl(Category $category): string
    {
        return CategoryResource::getUrl('edit', ['record' => $category]);
    }

    public function getItemEditUrl(Item $item): string
    {
        return ItemResource::getUrl('edit', ['record' => $item]);
    }

    protected function resolveImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) || str($path)->startsWith('data:')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        if (Storage::disk('local')->exists($path)) {
            try {
                return Storage::disk('local')->temporaryUrl($path, now()->addMinutes(30));
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }
}
