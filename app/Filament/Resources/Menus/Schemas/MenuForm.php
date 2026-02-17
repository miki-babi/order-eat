<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Toggle::make('is_featured')
                    ->required()
                    ->default(false),
                FileUpload::make('image_path')
                    ->image()
                    ->disk('public')
                    ->directory('menu/categories'),
                Repeater::make('items')
                    ->relationship('items')
                    ->label('Items')
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->orderColumn('display_order')
                    ->collapsed()
                    ->reorderableWithDragAndDrop()
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
                            ->required()
                            ->default(true),
                        Toggle::make('is_featured')
                            ->required()
                            ->default(false),
                        TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Repeater::make('itemImages')
                            ->relationship('itemImages')
                            ->label('Item Images')
                            ->itemLabel(fn (array $state): string => ($state['is_primary'] ?? false) ? 'Primary image' : 'Image')
                            ->collapsed()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->image()
                                    ->disk('public')
                                    ->directory('menu/items')
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
