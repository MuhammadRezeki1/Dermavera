<?php

namespace App\Filament\Resources\CriterionWeights\Pages;

use App\Filament\Resources\CriterionWeights\CriterionWeightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCriterionWeights extends ListRecords
{
    protected static string $resource = CriterionWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
