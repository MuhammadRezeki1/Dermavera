<?php

namespace App\Filament\Resources\DatasetVersions\Tables;

use App\Models\DatasetVersion;
use App\Services\DatasetVersionService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DatasetVersionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('activated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('hash')
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
                Action::make('activate')
                    ->label('Aktifkan')
                    ->requiresConfirmation()
                    ->visible(fn (DatasetVersion $record) => $record->status !== 'active')
                    ->action(fn (DatasetVersion $record) => app(DatasetVersionService::class)->activate($record)),
            ])
            ->toolbarActions([]);
    }
}
