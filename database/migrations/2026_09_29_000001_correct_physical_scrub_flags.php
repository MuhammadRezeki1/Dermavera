<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $normalizedName = new Expression("TRIM(TRAILING '.' FROM inci_name)");

        DB::table('ingredients')
            ->whereIn($normalizedName, ['CELLULOSE', 'CELLULOSE GUM', 'HYDROXYPROPYL METHYLCELLULOSE', 'SILICA', 'CORN STARCH', 'ZEA MAYS STARCH', 'MAGNESIUM POTASSIUM FLUOROSILICATE'])
            ->update(['is_physical_scrub' => false]);

        DB::table('ingredients')
            ->whereIn($normalizedName, ['PUMICE', 'PERLITE', 'HYDRATED SILICA', 'MICROCRYSTALLINE CELLULOSE', 'POLYETHYLENE', 'SYNTHETIC WAX'])
            ->update(['is_physical_scrub' => true]);
    }

    public function down(): void
    {
        // This data correction intentionally has no destructive rollback.
    }
};
