<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->index();
            $table->string('pseudonym')->nullable()->unique();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('official_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('catalog_code')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('target_claim')->nullable();
            $table->boolean('rinse_off')->default(true);
            $table->string('status')->default('draft')->index();
            $table->string('evidence_status')->default('PENDING_LABEL_VERIFICATION');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['brand_id', 'name']);
        });

        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('size_value', 10, 2)->nullable();
            $table->string('size_unit', 10)->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->string('bpom_no')->nullable()->index();
            $table->string('package_type')->default('tube');
            $table->unsignedTinyInteger('packaging_score')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['product_variant_id', 'size_value', 'size_unit']);
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->string('title');
            $table->string('publisher')->nullable();
            $table->string('type');
            $table->date('published_at')->nullable();
            $table->dateTimeTz('accessed_at');
            $table->string('checksum')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamps();
            $table->unique(['url', 'accessed_at']);
        });

        Schema::create('formula_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('product_sku_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('version_label');
            $table->longText('inci_raw');
            $table->longText('inci_normalized')->nullable();
            $table->decimal('ph_min', 4, 2)->nullable();
            $table->decimal('ph_max', 4, 2)->nullable();
            $table->dateTimeTz('verified_at')->nullable();
            $table->string('verification_status');
            $table->string('status')->default('draft')->index();
            $table->string('bpom_number')->nullable()->index();
            $table->string('registered_name')->nullable();
            $table->string('package_size')->nullable();
            $table->timestamps();
            $table->unique(['product_variant_id', 'version_label']);
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('inci_name')->unique();
            $table->string('display_name')->nullable();
            $table->string('function_group')->nullable()->index();
            $table->boolean('is_fragrance')->default(false);
            $table->boolean('is_menthol')->default(false);
            $table->boolean('is_physical_scrub')->default(false);
            $table->boolean('is_exfoliant')->default(false);
            $table->timestamps();
        });

        Schema::create('formula_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('ordinal');
            $table->decimal('concentration_value', 10, 4)->nullable();
            $table->string('concentration_unit', 20)->nullable();
            $table->string('concentration_status')->default('not_declared');
            $table->timestamps();
            $table->unique(['formula_version_id', 'ordinal']);
        });

        Schema::create('product_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('evidence_type');
            $table->string('verification_status');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['product_variant_id', 'source_id', 'evidence_type']);
        });

        Schema::create('price_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sku_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('seller');
            $table->dateTimeTz('observed_at')->index();
            $table->boolean('promo')->default(false);
            $table->decimal('normal_price', 14, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('criterion_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->string('label');
            $table->text('operational_definition');
            $table->timestamps();
            $table->unique(['criterion_id', 'score']);
        });

        Schema::create('weight_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version')->unique();
            $table->text('source');
            $table->dateTimeTz('validated_at');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('criterion_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weight_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained()->restrictOnDelete();
            $table->decimal('raw_value', 8, 4);
            $table->decimal('normalized_value', 12, 10);
            $table->timestamps();
            $table->unique(['weight_set_id', 'criterion_id']);
        });

        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('category')->index();
            $table->unsignedInteger('priority');
            $table->string('severity');
            $table->text('explanation');
            $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('version');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->cascadeOnDelete();
            $table->string('field');
            $table->string('operator');
            $table->json('value_json');
            $table->timestamps();
        });

        Schema::create('rule_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->cascadeOnDelete();
            $table->string('action_type');
            $table->string('target')->nullable();
            $table->json('value_json');
            $table->timestamps();
        });

        Schema::create('dataset_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique();
            $table->string('status')->default('draft')->index();
            $table->dateTimeTz('activated_at')->nullable();
            $table->char('hash', 64)->unique();
            $table->text('notes')->nullable();
            $table->json('snapshot');
            $table->timestamps();
        });

        Schema::create('product_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained()->cascadeOnDelete();
            $table->string('context_key');
            $table->unsignedTinyInteger('score');
            $table->text('rationale');
            $table->timestamps();
            $table->unique(['dataset_version_id', 'product_variant_id', 'criterion_id', 'context_key']);
        });

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dataset_version_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('age_group');
            $table->string('algorithm_version');
            $table->dateTimeTz('consent_at');
            $table->json('input_snapshot');
            $table->string('status')->default('started')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('consultation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->string('question_code');
            $table->json('selected_value');
            $table->timestamps();
            $table->unique(['consultation_id', 'question_code']);
        });

        Schema::create('safety_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('red_flag');
            $table->json('hard_constraint_codes');
            $table->string('outcome')->index();
            $table->json('explanation_codes');
            $table->timestamps();
        });

        Schema::create('recommendation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('weight_set_id')->nullable()->constrained()->restrictOnDelete();
            $table->uuid('idempotency_key')->unique();
            $table->dateTimeTz('started_at');
            $table->dateTimeTz('completed_at')->nullable();
            $table->string('status')->index();
            $table->json('calculation_snapshot')->nullable();
            $table->timestamps();
        });

        Schema::create('recommendation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_sku_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('formula_version_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->json('raw_scores');
            $table->json('normalized_scores');
            $table->json('contributions');
            $table->decimal('final_score', 12, 10)->nullable();
            $table->string('eligibility_status')->index();
            $table->json('explanation_codes');
            $table->json('warnings');
            $table->timestamps();
            $table->unique(['recommendation_run_id', 'product_variant_id', 'formula_version_id'], 'run_variant_formula_unique');
        });

        Schema::create('expert_validations', function (Blueprint $table) {
            $table->id();
            $table->text('summary');
            $table->date('validated_at');
            $table->string('scope');
            $table->boolean('quote_permission')->default(false);
            $table->string('evidence_path')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('request_id')->nullable()->index();
            $table->ipAddress('ip_address')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->index(['auditable_type', 'auditable_id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE criterion_weights ADD CONSTRAINT criterion_weights_range CHECK (normalized_value > 0 AND normalized_value <= 1)');
            DB::statement('ALTER TABLE criterion_scales ADD CONSTRAINT criterion_scales_one_to_five CHECK (score BETWEEN 1 AND 5)');
            DB::statement('ALTER TABLE product_scores ADD CONSTRAINT product_scores_one_to_five CHECK (score BETWEEN 1 AND 5)');
            DB::statement('ALTER TABLE product_skus ADD CONSTRAINT packaging_score_one_to_five CHECK (packaging_score BETWEEN 1 AND 5)');
            DB::statement('ALTER TABLE price_observations ADD CONSTRAINT positive_price CHECK (amount >= 0 AND (normal_price IS NULL OR normal_price >= 0))');
            DB::statement('ALTER TABLE formula_versions ADD CONSTRAINT valid_ph_range CHECK ((ph_min IS NULL AND ph_max IS NULL) OR (ph_min BETWEEN 0 AND 14 AND ph_max BETWEEN 0 AND 14 AND ph_min <= ph_max))');
            DB::statement("CREATE UNIQUE INDEX formula_versions_one_active_per_sku ON formula_versions (product_sku_id) WHERE status = 'active'");
            DB::statement("CREATE UNIQUE INDEX dataset_versions_one_active ON dataset_versions ((status)) WHERE status = 'active'");
            DB::statement('CREATE UNIQUE INDEX weight_sets_one_active ON weight_sets ((is_active)) WHERE is_active = TRUE');
        }
    }

    public function down(): void
    {
        foreach (['audit_logs', 'expert_validations', 'recommendation_results', 'recommendation_runs',
            'safety_assessments', 'consultation_answers', 'consultations', 'product_scores', 'dataset_versions',
            'rule_actions', 'rule_conditions', 'rules', 'criterion_weights', 'weight_sets', 'criterion_scales',
            'criteria', 'price_observations', 'product_evidence', 'formula_ingredients', 'ingredients',
            'formula_versions', 'sources', 'product_skus', 'product_variants', 'brands'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'pseudonym']);
        });
    }
};
