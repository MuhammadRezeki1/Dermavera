<?php

namespace App\Filament\Resources\ProductEvidence;

use App\Filament\Resources\ProductEvidence\Pages\CreateProductEvidence;
use App\Filament\Resources\ProductEvidence\Pages\EditProductEvidence;
use App\Filament\Resources\ProductEvidence\Pages\ListProductEvidence;
use App\Filament\Resources\ProductEvidence\Schemas\ProductEvidenceForm;
use App\Filament\Resources\ProductEvidence\Tables\ProductEvidenceTable;
use App\Models\ProductEvidence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductEvidenceResource extends Resource
{
    protected static ?string $model = ProductEvidence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductEvidenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductEvidenceTable::configure($table);
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
            'index' => ListProductEvidence::route('/'),
            'create' => CreateProductEvidence::route('/create'),
            'edit' => EditProductEvidence::route('/{record}/edit'),
        ];
    }
}
