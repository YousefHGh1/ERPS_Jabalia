<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait PreventsNPlusOne
{
    /**
     * Detect and log potential N+1 queries
     *
     * @param string $model
     * @param string $relation
     * @return void
     */
    protected function detectNPlusOne(string $model, string $relation): void
    {
        if (config('app.debug') && config('app.env') !== 'production') {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $caller = $backtrace[1] ?? [];

            Log::warning('Potential N+1 Query detected', [
                'model' => $model,
                'relation' => $relation,
                'file' => $caller['file'] ?? 'unknown',
                'line' => $caller['line'] ?? 'unknown',
                'function' => $caller['function'] ?? 'unknown',
            ]);
        }
    }

    /**
     * Ensure relationships are loaded
     *
     * @param Model $model
     * @param string|array $relations
     * @return void
     */
    protected function ensureLoaded(Model $model, $relations): void
    {
        $relations = (array) $relations;

        foreach ($relations as $relation) {
            if (!$model->relationLoaded($relation)) {
                $this->detectNPlusOne(get_class($model), $relation);
                $model->load($relation);
            }
        }
    }

    /**
     * Ensure collection relationships are loaded
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param string|array $relations
     * @return void
     */
    protected function ensureCollectionLoaded($collection, $relations): void
    {
        $relations = (array) $relations;

        foreach ($relations as $relation) {
            if (!$collection->first()?->relationLoaded($relation)) {
                $model = $collection->first();
                if ($model) {
                    $this->detectNPlusOne(get_class($model), $relation);
                }
                $collection->load($relation);
            }
        }
    }
}
