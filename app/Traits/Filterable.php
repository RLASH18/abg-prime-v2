<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply search filter to query
     * 
     * @param Builder $query
     * @param string|null $search
     * @param array $searchableColumns
     * @return Builder
     */
    protected function applySearchFilter(Builder $query, ?string $search, array $searchableColumns): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $index => $column) {
                if ($index === 0) {
                    $q->where($column, 'like', "%{$search}%");
                } else {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            }
        });
    }

    /**
     * Apply exact match filter to query
     * 
     * @param Builder $query
     * @param string $column
     * @param mixed $value
     * @return Builder
     */
    protected function applyExactFilter(Builder $query, string $column, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where($column, $value);
    }

    /**
     * Apply custom filter with callback
     * 
     * @param Builder $query
     * @param mixed $value
     * @param callable $callback
     * @return Builder
     */
    protected function applyCustomFilter(Builder $query, mixed $value, callable $callback): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $callback($query, $value);
    }
}
