<?php

namespace App\Filament\Resources\DatasetVersions\Pages;

use App\Filament\Resources\DatasetVersions\DatasetVersionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDatasetVersion extends EditRecord
{
    protected static string $resource = DatasetVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
