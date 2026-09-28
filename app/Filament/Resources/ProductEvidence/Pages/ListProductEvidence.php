<?php

namespace App\Filament\Resources\ProductEvidence\Pages;

use App\Filament\Resources\ProductEvidence\ProductEvidenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductEvidence extends ListRecords
{
    protected static string $resource = ProductEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
