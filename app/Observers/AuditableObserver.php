<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\DatasetVersion;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $this->write($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->write($model, 'updated', array_intersect_key($model->getOriginal(), $model->getChanges()), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->write($model, 'deleted', $model->getOriginal(), null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    private function write(Model $model, string $action, ?array $before, ?array $after): void
    {
        AuditLog::create(['user_id' => auth()->id(), 'action' => class_basename($model).'.'.$action, 'auditable_type' => $model::class, 'auditable_id' => $model->getKey(), 'before' => $before, 'after' => $after, 'request_id' => app()->runningInConsole() ? null : request()->header('X-Request-ID'), 'ip_address' => app()->runningInConsole() ? null : request()->ip()]);
        if (! $model instanceof DatasetVersion) {
            DatasetVersion::where('status', 'active')->update(['status' => 'stale', 'updated_at' => now()]);
        }
    }
}
