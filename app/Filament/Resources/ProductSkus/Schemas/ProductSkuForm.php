<?php

namespace App\Filament\Resources\ProductSkus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductSkuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_variant_id')
                    ->relationship('variant', 'name')
                    ->required()
                    ->searchable(),
                TextInput::make('size_value')
                    ->numeric(),
                TextInput::make('size_unit'),
                TextInput::make('barcode'),
                TextInput::make('bpom_no'),
                TextInput::make('package_type')
                    ->required()
                    ->default('tube'),
                TextInput::make('packaging_score')
                    ->required()
                    ->numeric()
                    ->minValue(1)->maxValue(5)
                    ->default(3),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
