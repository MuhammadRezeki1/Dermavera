<?php

namespace App\Providers;

use App\Domain\Recommendation\Contracts\SafetyGateContract;
use App\Domain\Recommendation\Contracts\SawCalculatorContract;
use App\Models\Brand;
use App\Models\Criterion;
use App\Models\CriterionWeight;
use App\Models\DatasetVersion;
use App\Models\FormulaIngredient;
use App\Models\FormulaVersion;
use App\Models\Ingredient;
use App\Models\PriceObservation;
use App\Models\ProductEvidence;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Rule;
use App\Models\Source;
use App\Models\WeightSet;
use App\Observers\AuditableObserver;
use App\Services\SafetyGateService;
use App\Services\SawCalculator;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SafetyGateContract::class, SafetyGateService::class);
        $this->app->bind(SawCalculatorContract::class, SawCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        RateLimiter::for('consultation', fn (Request $request) => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip()));
        foreach ([Brand::class, ProductVariant::class, ProductSku::class, FormulaVersion::class, FormulaIngredient::class, Ingredient::class, ProductEvidence::class, Source::class, PriceObservation::class, Criterion::class, Rule::class, WeightSet::class, CriterionWeight::class, DatasetVersion::class] as $model) {
            $model::observe(AuditableObserver::class);
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
