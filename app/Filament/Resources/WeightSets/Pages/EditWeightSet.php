<?php

namespace App\Filament\Resources\WeightSets\Pages;

use App\Filament\Resources\WeightSets\WeightSetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWeightSet extends EditRecord
{
    protected static string $resource = WeightSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
