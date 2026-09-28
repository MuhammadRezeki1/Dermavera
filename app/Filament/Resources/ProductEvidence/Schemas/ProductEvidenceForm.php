<?php

namespace App\Filament\Resources\ProductEvidence\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductEvidenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_variant_id')
                    ->relationship('variant', 'name')
                    ->required()
                    ->searchable(),
                Select::make('source_id')
                    ->relationship('source', 'title')
                    ->required()
                    ->searchable(),
                TextInput::make('evidence_type')
                    ->required(),
                TextInput::make('verification_status')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
