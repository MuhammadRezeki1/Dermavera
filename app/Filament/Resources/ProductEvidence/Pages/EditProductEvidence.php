<?php

namespace App\Filament\Resources\ProductEvidence\Pages;

use App\Filament\Resources\ProductEvidence\ProductEvidenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductEvidence extends EditRecord
{
    protected static string $resource = ProductEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
