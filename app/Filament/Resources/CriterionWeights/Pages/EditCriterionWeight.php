<?php

namespace App\Filament\Resources\CriterionWeights\Pages;

use App\Filament\Resources\CriterionWeights\CriterionWeightResource;
use App\Models\CriterionWeight;
use App\Services\WeightSetService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LogicException;

class EditCriterionWeight extends EditRecord
{
    protected static string $resource = CriterionWeightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof CriterionWeight) {
            throw new LogicException('Bobot kriteria gagal dimuat setelah disimpan.');
        }

        app(WeightSetService::class)->normalize($record->weightSet);
    }
}
