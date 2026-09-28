<?php

namespace App\Filament\Resources\DatasetVersions;

use App\Filament\Resources\DatasetVersions\Pages\ListDatasetVersions;
use App\Filament\Resources\DatasetVersions\Schemas\DatasetVersionForm;
use App\Filament\Resources\DatasetVersions\Tables\DatasetVersionsTable;
use App\Models\DatasetVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DatasetVersionResource extends Resource
{
    protected static ?string $model = DatasetVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DatasetVersionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DatasetVersionsTable::configure($table);
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
            'index' => ListDatasetVersions::route('/'),
        ];
    }
}
