<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WithEagerLoading
{
    /**
     * Get default eager load relationships for the model
     *
     * @return array
     */
    abstract protected function getDefaultEagerLoads(): array;

    /**
     * Apply eager loading to query
     *
     * @param Builder $query
     * @param array|null $additionalRelations
     * @return Builder
     */
    protected function applyEagerLoading(Builder $query, ?array $additionalRelations = null): Builder
    {
        $relations = $this->getDefaultEagerLoads();

        if ($additionalRelations) {
            $relations = array_merge($relations, $additionalRelations);
        }

        return $query->with($relations);
    }

    /**
     * Apply eager loading with count
     *
     * @param Builder $query
     * @param array $countRelations
     * @return Builder
     */
    protected function applyEagerLoadingWithCount(Builder $query, array $countRelations = []): Builder
    {
        $relations = $this->getDefaultEagerLoads();

        if (!empty($countRelations)) {
            return $query->with($relations)->withCount($countRelations);
        }

        return $query->with($relations);
    }
}
