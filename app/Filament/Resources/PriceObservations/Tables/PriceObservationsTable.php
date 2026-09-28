<?php

namespace App\Filament\Resources\PriceObservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceObservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku.variant.catalog_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sku.size_value')
                    ->label('Ukuran')
                    ->formatStateUsing(fn ($state, $record): string => (float) $state.' '.$record->sku->size_unit),
                TextColumn::make('source.title')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('seller')
                    ->searchable(),
                TextColumn::make('observed_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('promo')
                    ->boolean(),
                TextColumn::make('normal_price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('unit_price_per_100')
                    ->label('Per 100')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('evidence_status')
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_available')
                    ->boolean(),
                IconColumn::make('ranking_eligible')
                    ->boolean(),
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
