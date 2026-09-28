<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('url')
                    ->required()
                    ->url()
                    ->rule('starts_with:https://')
                    ->maxLength(2048)
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('publisher'),
                TextInput::make('type')
                    ->required(),
                DatePicker::make('published_at'),
                DateTimePicker::make('accessed_at')
                    ->required(),
                TextInput::make('checksum'),
                TextInput::make('evidence_path'),
            ]);
    }
}
