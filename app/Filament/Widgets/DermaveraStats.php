<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use App\Models\DatasetVersion;
use App\Models\PriceObservation;
use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DermaveraStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $dataset = DatasetVersion::where('status', 'active')->firstOrFail();

        return [
            Stat::make('Produk aktif', ProductVariant::where('is_active', true)->count())
                ->description('Varian lolos status publik')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
            Stat::make('Observasi harga', PriceObservation::count())
                ->description('Catatan harga bertanggal')
                ->descriptionIcon('heroicon-m-banknotes'),
            Stat::make('Konsultasi', Consultation::count())
                ->description('Snapshot keputusan tersimpan')
                ->descriptionIcon('heroicon-m-clipboard-document-check'),
            Stat::make('Dataset aktif', $dataset->version)
                ->description('Hash '.substr($dataset->hash, 0, 10).'…')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('primary'),
        ];
    }
}
