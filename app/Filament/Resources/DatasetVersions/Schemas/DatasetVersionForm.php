<?php

namespace App\Filament\Resources\DatasetVersions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DatasetVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('version')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('activated_at'),
                TextInput::make('hash')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Textarea::make('snapshot')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
