<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{

    /**
     * Aplica búsqueda por texto en múltiples columnas
     */
    public function applySearch(Builder $query, ?string $search, array $columns): Builder
    {
        if (empty($search)) return $query;

        return $query->where(function ($q) use ($search, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }
        });
    }

    /**
     * Aplica filtros exactos key => value
     */
    public function applyFilters(Builder $query, array $filters, array $allowed): Builder
    {
        foreach ($allowed as $field) {
            if (!empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        return $query;
    }

}
