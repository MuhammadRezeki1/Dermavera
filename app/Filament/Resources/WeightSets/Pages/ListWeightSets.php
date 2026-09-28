<?php

namespace App\Filament\Resources\WeightSets\Pages;

use App\Filament\Resources\WeightSets\WeightSetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeightSets extends ListRecords
{
    protected static string $resource = WeightSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
