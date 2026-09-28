<?php

namespace App\Filament\Resources\Ingredients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IngredientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('inci_name')
                    ->required(),
                TextInput::make('display_name'),
                TextInput::make('function_group'),
                Toggle::make('is_fragrance')
                    ->required(),
                Toggle::make('is_menthol')
                    ->required(),
                Toggle::make('is_physical_scrub')
                    ->required(),
                Toggle::make('is_exfoliant')
                    ->required(),
            ]);
    }
}
