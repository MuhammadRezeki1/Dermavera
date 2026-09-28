<?php

namespace App\Filament\Resources\FormulaVersions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FormulaVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_variant_id')
                    ->relationship('variant', 'name')
                    ->required()
                    ->searchable(),
                Select::make('product_sku_id')
                    ->relationship('sku', 'bpom_no')
                    ->searchable(),
                Select::make('source_id')
                    ->relationship('source', 'title'),
                TextInput::make('version_label')
                    ->required(),
                Textarea::make('inci_raw')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('inci_normalized')
                    ->columnSpanFull(),
                TextInput::make('ph_min')
                    ->numeric()->minValue(0)->maxValue(14)->lte('ph_max'),
                TextInput::make('ph_max')
                    ->numeric()->minValue(0)->maxValue(14)->gte('ph_min'),
                DateTimePicker::make('verified_at'),
                TextInput::make('verification_status')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('bpom_number'),
                TextInput::make('registered_name'),
                TextInput::make('package_size'),
            ]);
    }
}
