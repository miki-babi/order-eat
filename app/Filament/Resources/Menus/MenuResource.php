<?php

namespace App\Filament\Resources\Menus;

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Menus\Pages\CreateMenu;
use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\Schemas\MenuForm;
use App\Filament\Resources\Menus\Tables\MenusTable;
use App\Models\Category;
use App\Models\Item;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MenuForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('name')
                    ->hiddenLabel()
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->placeholder('No category description')
                    ->columnSpanFull(),
                RepeatableEntry::make('items')
                    ->label('Menu Items')
                    ->state(fn (Category $record) => $record
                        ->items()
                        ->with('itemImages')
                        ->orderBy('display_order')
                        ->orderBy('name')
                        ->get())
                    ->placeholder('No items in this category yet.')
                    ->contained(false)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('price')
                            ->money('USD'),
                        TextEntry::make('description')
                            ->placeholder('No item description')
                            ->columnSpanFull(),
                        RepeatableEntry::make('itemImages')
                            ->label('Images')
                            ->state(fn (Item $record) => $record
                                ->itemImages()
                                ->orderByDesc('is_primary')
                                ->orderBy('id')
                                ->get())
                            ->placeholder('No images yet.')
                            ->contained(false)
                            ->grid(4)
                            ->schema([
                                ImageEntry::make('image_path')
                                    ->hiddenLabel()
                                    ->square()
                                    ->imageHeight(96),
                                TextEntry::make('is_primary')
                                    ->label('Primary')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                            ])
                            ->columnSpanFull(),
                        Actions::make([
                            Action::make('editItem')
                                ->label('Edit Item')
                                ->icon(Heroicon::OutlinedPencilSquare)
                                ->schema([
                                    TextInput::make('name')
                                        ->required(),
                                    TextInput::make('price')
                                        ->required()
                                        ->numeric()
                                        ->prefix('$'),
                                    Textarea::make('description')
                                        ->columnSpanFull(),
                                    Toggle::make('is_active')
                                        ->required(),
                                    Toggle::make('is_featured')
                                        ->required(),
                                    TextInput::make('display_order')
                                        ->required()
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->fillForm(fn (Item $record): array => [
                                    'name' => $record->name,
                                    'price' => $record->price,
                                    'description' => $record->description,
                                    'is_active' => $record->is_active,
                                    'is_featured' => $record->is_featured,
                                    'display_order' => $record->display_order,
                                ])
                                ->action(function (array $data, Item $record): void {
                                    $record->update($data);
                                })
                                ->successNotificationTitle('Item updated'),
                            Action::make('uploadImage')
                                ->label('Upload Image')
                                ->icon(Heroicon::OutlinedPhoto)
                                ->schema([
                                    FileUpload::make('image_path')
                                        ->image()
                                        ->disk('public')
                                        ->directory('menu/items')
                                        ->required(),
                                    Toggle::make('is_primary')
                                        ->default(false),
                                ])
                                ->action(function (array $data, Item $record): void {
                                    if ($data['is_primary'] ?? false) {
                                        $record->itemImages()->update(['is_primary' => false]);
                                    }

                                    $record->itemImages()->create($data);
                                })
                                ->successNotificationTitle('Image uploaded'),
                            Action::make('openItemEditor')
                                ->label('Open Full Editor')
                                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                                ->url(fn (Item $record): string => ItemResource::getUrl('edit', ['record' => $record]))
                                ->openUrlInNewTab(),
                        ])
                            ->columnSpanFull()
                            ->alignEnd(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return MenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
