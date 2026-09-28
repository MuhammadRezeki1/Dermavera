<?php

namespace App\Filament\Resources\CriterionWeights\Pages;

use App\Filament\Resources\CriterionWeights\CriterionWeightResource;
use App\Models\CriterionWeight;
use App\Services\WeightSetService;
use Filament\Resources\Pages\CreateRecord;
use LogicException;

class CreateCriterionWeight extends CreateRecord
{
    protected static string $resource = CriterionWeightResource::class;

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['normalized_value'] = '1.0000000000';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof CriterionWeight) {
            throw new LogicException('Bobot kriteria gagal dimuat setelah dibuat.');
        }

        app(WeightSetService::class)->normalize($record->weightSet);
    }
}
