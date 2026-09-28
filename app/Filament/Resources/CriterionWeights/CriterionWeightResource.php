<?php

namespace App\Filament\Resources\CriterionWeights;

use App\Filament\Resources\CriterionWeights\Pages\CreateCriterionWeight;
use App\Filament\Resources\CriterionWeights\Pages\EditCriterionWeight;
use App\Filament\Resources\CriterionWeights\Pages\ListCriterionWeights;
use App\Filament\Resources\CriterionWeights\Schemas\CriterionWeightForm;
use App\Filament\Resources\CriterionWeights\Tables\CriterionWeightsTable;
use App\Models\CriterionWeight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CriterionWeightResource extends Resource
{
    protected static ?string $model = CriterionWeight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CriterionWeightForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CriterionWeightsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCriterionWeights::route('/'),
            'create' => CreateCriterionWeight::route('/create'),
            'edit' => EditCriterionWeight::route('/{record}/edit'),
        ];
    }
}
