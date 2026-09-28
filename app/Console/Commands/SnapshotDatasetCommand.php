<?php

namespace App\Console\Commands;

use App\Services\DatasetVersionService;
use Illuminate\Console\Command;

class SnapshotDatasetCommand extends Command
{
    protected $signature = 'dermavera:dataset:snapshot {version} {--activate} {--notes=}';

    protected $description = 'Membekukan produk, formula, aturan, dan bobot menjadi dataset yang dapat direproduksi.';

    public function handle(DatasetVersionService $service): int
    {
        $dataset = $service->snapshot($this->argument('version'), $this->option('notes'));
        if ($this->option('activate')) {
            $service->activate($dataset);
        } $this->info("Dataset {$dataset->version}: {$dataset->hash}");

        return self::SUCCESS;
    }
}
