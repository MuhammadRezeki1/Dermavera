<?php

namespace App\Filament\Resources\WeightSets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WeightSetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('version')
                    ->required(),
                Textarea::make('source')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('validated_at')
                    ->required(),
                Toggle::make('is_active')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
