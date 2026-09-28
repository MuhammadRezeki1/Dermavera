<?php

namespace App\Filament\Resources\ProductSkus;

use App\Filament\Resources\ProductSkus\Pages\CreateProductSku;
use App\Filament\Resources\ProductSkus\Pages\EditProductSku;
use App\Filament\Resources\ProductSkus\Pages\ListProductSkus;
use App\Filament\Resources\ProductSkus\Schemas\ProductSkuForm;
use App\Filament\Resources\ProductSkus\Tables\ProductSkusTable;
use App\Models\ProductSku;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductSkuResource extends Resource
{
    protected static ?string $model = ProductSku::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductSkuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductSkusTable::configure($table);
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
            'index' => ListProductSkus::route('/'),
            'create' => CreateProductSku::route('/create'),
            'edit' => EditProductSku::route('/{record}/edit'),
        ];
    }
}
