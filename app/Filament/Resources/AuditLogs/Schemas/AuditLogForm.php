<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('action')
                    ->required(),
                TextInput::make('auditable_type')
                    ->required(),
                TextInput::make('auditable_id')
                    ->numeric(),
                Textarea::make('before')
                    ->columnSpanFull(),
                Textarea::make('after')
                    ->columnSpanFull(),
                TextInput::make('request_id'),
                TextInput::make('ip_address'),
            ]);
    }
}
