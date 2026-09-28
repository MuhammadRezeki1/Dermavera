<?php

namespace App\Filament\Resources\Consultations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ConsultationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('dataset_version_id')
                    ->relationship('datasetVersion', 'id'),
                TextInput::make('age_group')
                    ->required(),
                TextInput::make('algorithm_version')
                    ->required(),
                DateTimePicker::make('consent_at')
                    ->required(),
                Textarea::make('input_snapshot')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('started'),
                DateTimePicker::make('expires_at'),
            ]);
    }
}
