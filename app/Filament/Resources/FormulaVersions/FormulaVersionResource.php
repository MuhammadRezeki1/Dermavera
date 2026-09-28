<?php

namespace App\Filament\Resources\FormulaVersions;

use App\Filament\Resources\FormulaVersions\Pages\CreateFormulaVersion;
use App\Filament\Resources\FormulaVersions\Pages\EditFormulaVersion;
use App\Filament\Resources\FormulaVersions\Pages\ListFormulaVersions;
use App\Filament\Resources\FormulaVersions\Schemas\FormulaVersionForm;
use App\Filament\Resources\FormulaVersions\Tables\FormulaVersionsTable;
use App\Models\FormulaVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FormulaVersionResource extends Resource
{
    protected static ?string $model = FormulaVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FormulaVersionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormulaVersionsTable::configure($table);
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
            'index' => ListFormulaVersions::route('/'),
            'create' => CreateFormulaVersion::route('/create'),
            'edit' => EditFormulaVersion::route('/{record}/edit'),
        ];
    }
}
