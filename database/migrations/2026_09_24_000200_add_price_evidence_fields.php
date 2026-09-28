<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('price_observations', function (Blueprint $table) {
            $table->decimal('unit_price_per_100', 14, 2)->nullable()->after('normal_price');
            $table->string('evidence_status')->default('unverified')->index()->after('unit_price_per_100');
            $table->boolean('is_available')->default(false)->index()->after('evidence_status');
            $table->boolean('ranking_eligible')->default(false)->index()->after('is_available');
            $table->text('notes')->nullable()->after('ranking_eligible');
            $table->unique(['product_sku_id', 'source_id', 'observed_at'], 'price_observations_unique_evidence');
        });

        Schema::table('recommendation_results', function (Blueprint $table) {
            $table->foreignId('price_observation_id')->nullable()->after('formula_version_id')->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('price_observation_id');
        });

        Schema::table('price_observations', function (Blueprint $table) {
            $table->dropUnique('price_observations_unique_evidence');
            $table->dropColumn(['unit_price_per_100', 'evidence_status', 'is_available', 'ranking_eligible', 'notes']);
        });
    }
};
