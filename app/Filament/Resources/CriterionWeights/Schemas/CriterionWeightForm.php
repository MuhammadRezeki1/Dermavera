<?php

namespace App\Filament\Resources\CriterionWeights\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CriterionWeightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('weight_set_id')
                    ->relationship('weightSet', 'name')
                    ->required()
                    ->searchable(),
                Select::make('criterion_id')
                    ->relationship('criterion', 'name')
                    ->required(),
                TextInput::make('raw_value')
                    ->required()
                    ->numeric()
                    ->minValue(0.000001)
                    ->helperText('Nilai normal akan dihitung ulang otomatis dari seluruh bobot mentah dalam versi yang sama.'),
                TextInput::make('normalized_value')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
