<?php

namespace App\Filament\Resources\WeightSets;

use App\Filament\Resources\WeightSets\Pages\CreateWeightSet;
use App\Filament\Resources\WeightSets\Pages\EditWeightSet;
use App\Filament\Resources\WeightSets\Pages\ListWeightSets;
use App\Filament\Resources\WeightSets\Schemas\WeightSetForm;
use App\Filament\Resources\WeightSets\Tables\WeightSetsTable;
use App\Models\WeightSet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeightSetResource extends Resource
{
    protected static ?string $model = WeightSet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WeightSetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeightSetsTable::configure($table);
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
            'index' => ListWeightSets::route('/'),
            'create' => CreateWeightSet::route('/create'),
            'edit' => EditWeightSet::route('/{record}/edit'),
        ];
    }
}
