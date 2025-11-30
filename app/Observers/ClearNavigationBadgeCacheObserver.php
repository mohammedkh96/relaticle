<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Observer to clear navigation badge cache when models are modified
 */
class ClearNavigationBadgeCacheObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->clearCache($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->clearCache($model);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->clearCache($model);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->clearCache($model);
    }

    /**
     * Clear the navigation badge cache for the model
     */
    protected function clearCache(Model $model): void
    {
        $modelName = strtolower(class_basename($model));

        // Clear global cache
        Cache::forget("nav_badge_{$modelName}");

        // Clear tenant-specific cache if model has team_id
        if (isset($model->team_id)) {
            Cache::forget("nav_badge_{$modelName}_{$model->team_id}");
        }

        // Also clear the global tenant cache
        Cache::forget("nav_badge_{$modelName}_global");
    }
}
