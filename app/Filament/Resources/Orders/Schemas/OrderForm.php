<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('special_instructions')
                    ->columnSpanFull(),
                TextInput::make('payment_method'),
                TextInput::make('payment_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('delivery_method')
                    ->required()
                    ->default('self-pickup'),
                DateTimePicker::make('order_time')
                    ->required(),
            ]);
    }
}
