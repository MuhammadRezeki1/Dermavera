<?php

namespace App\Filament\Resources\DatasetVersions\Pages;

use App\Filament\Resources\DatasetVersions\DatasetVersionResource;
use App\Services\DatasetVersionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListDatasetVersions extends ListRecords
{
    protected static string $resource = DatasetVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('freeze')
                ->label('Bekukan versi baru')
                ->schema([
                    TextInput::make('version')->required()->unique('dataset_versions', 'version'),
                    Textarea::make('notes'),
                ])
                ->action(fn (array $data) => app(DatasetVersionService::class)->snapshot($data['version'], $data['notes'] ?? null)),
        ];
    }
}
