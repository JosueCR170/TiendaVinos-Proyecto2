<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ApiQueryTrait
{
    protected function applySearch(
        Builder $query,
        Request $request,
        array $searchableFields
    ): Builder {

        if (!$request->filled('search')) {
            return $query;
        }

        $search = $request->search;

        $query->where(function ($q) use ($searchableFields, $search) {

            foreach ($searchableFields as $field) {

                $q->orWhere(
                    $field,
                    'LIKE',
                    "%{$search}%"
                );

            }

        });

        return $query;
    }

    protected function applyFilters(
        Builder $query,
        Request $request,
        array $allowedFilters
    ): Builder {

        foreach ($allowedFilters as $filter) {

            if ($request->filled($filter)) {

                $query->where(
                    $filter,
                    $request->input($filter)
                );

            }

        }

        return $query;
    }

    protected function applySorting(
        Builder $query,
        Request $request,
        array $allowedSorts,
        string $defaultSort = 'id'
    ): Builder {

        $sortBy = $request->input('sort_by', $defaultSort);
        $direction = strtolower(
            $request->input('direction', 'asc')
        );

        if (
            in_array($sortBy, $allowedSorts) &&
            in_array($direction, ['asc', 'desc'])
        ) {
            $query->orderBy($sortBy, $direction);
        }

        return $query;
    }

    protected function getPerPage(
        Request $request,
        int $default = 10,
        int $min = 5,
        int $max = 100
    ): int {

        return min(
            max(
                $request->integer('per_page', $default),
                $min
            ),
            $max
        );
    }
}