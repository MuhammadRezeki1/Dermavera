<?php

namespace App\Filament\Resources\PriceObservations\Schemas;

use App\Models\ProductSku;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PriceObservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_sku_id')
                    ->relationship('sku', 'id')
                    ->getOptionLabelFromRecordUsing(fn (ProductSku $record): string => $record->variant->catalog_code.' — '.(float) $record->size_value.' '.$record->size_unit)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('SKU produk'),
                Select::make('source_id')
                    ->relationship('source', 'title')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('seller')
                    ->required(),
                DateTimePicker::make('observed_at')
                    ->required()
                    ->helperText('Tanggal saat harga benar-benar diperiksa. Batas kesegaran saat ini '.config('dermavera.price_max_age_days', 30).' hari.'),
                Toggle::make('promo')
                    ->required(),
                TextInput::make('normal_price')
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('unit_price_per_100')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Harga transaksi dibagi ukuran × 100.'),
                TextInput::make('evidence_status')
                    ->required(),
                Toggle::make('is_available')
                    ->label('Tersedia saat observasi')
                    ->required(),
                Toggle::make('ranking_eligible')
                    ->label('Layak untuk ranking')
                    ->helperText('Aktifkan hanya jika bukti harga valid dan produk tersedia. Buat observasi baru untuk memperbarui harga tanpa mengubah riwayat lama.')
                    ->required(),
                TextInput::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
