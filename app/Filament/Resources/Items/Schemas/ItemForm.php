<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
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
                Repeater::make('itemImages')
                    ->relationship('itemImages')
                    ->label('Item Images')
                    ->helperText('Upload one or more images for this menu item.')
                    ->itemLabel(fn (array $state): string => ($state['is_primary'] ?? false) ? 'Primary image' : 'Image')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('menu/items')
                            ->required(),
                        Toggle::make('is_primary')
                            ->label('Primary')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
