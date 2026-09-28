<?php

namespace App\Filament\Resources\Rules\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('priority')
                    ->required()
                    ->numeric(),
                TextInput::make('severity')
                    ->required(),
                Textarea::make('explanation')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('source_id')
                    ->numeric(),
                TextInput::make('version')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
