<?php

namespace App\Filament\Resources\FormulaVersions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FormulaVersionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_variant_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('product_sku_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('source.title')
                    ->searchable(),
                TextColumn::make('version_label')
                    ->searchable(),
                TextColumn::make('ph_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ph_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('verification_status')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('bpom_number')
                    ->searchable(),
                TextColumn::make('registered_name')
                    ->searchable(),
                TextColumn::make('package_size')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
