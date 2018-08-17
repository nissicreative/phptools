<?php
namespace Nissi\Traits;

use Carbon\Carbon;
use Nissi\Database\QueryFilters;

trait QueryScopes
{
    /*
    |--------------------------------------------------------------------------
    | WHERE Clauses
    |--------------------------------------------------------------------------
     */

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }

    // Time-based
    // -------------------------------------------------- //
    public function scopeCreatedSince($query, $dt)
    {
        return $query->where($this::CREATED_AT, '>=', Carbon::parse($dt));
    }

    public function scopeCreatedBefore($query, $dt)
    {
        return $query->where($this::CREATED_AT, '<', Carbon::parse($dt));
    }

    public function scopeUpdatedSince($query, $dt)
    {
        return $query->where($this::UPDATED_AT, '>=', Carbon::parse($dt));
    }

    public function scopeUpdatedBefore($query, $dt)
    {
        return $query->where($this::UPDATED_AT, '<', Carbon::parse($dt));
    }

    public function scopeCreatedOn($query, $dt)
    {
        return $query->whereDate($this::CREATED_AT, $dt);
    }

    public function scopeUpdatedOn($query, $dt)
    {
        return $query->whereDate($this::UPDATED_AT, $dt);
    }

    // Simple Search Query
    // -------------------------------------------------- //
    public function scopeSearch($query, $q)
    {
        // Query string
        $q = trim($q);

        if (empty($q)) {
            return $query;
        }

        // Sample record so we can get table info
        $sample = static::first() ?: new static();

        // Search all columns unless a `searchable` array is defined in the model
        $defaultColumns = collect($sample->toArray())
            ->keys()
            ->reject(function ($column) {
                return in_array($column, ['id', 'password', 'remember_token'])
                || ends_with($column, '_id')
                || ends_with($column, '_at')
                || ends_with($column, '_on');
            })
            ->map(function ($key) {
                return '`' . $key . '`';
            });

        $searchColumns = collect(array_get($this->searchable, 'columns', $defaultColumns));

        // Use joins if provided in `searchable` array; if not, an empty array
        $joins = array_get($this->searchable, 'joins', []);

        // Split the search into tokens
        $terms = preg_split('~\W+~', $q);

        // Scope the SELECT clause to the current model's table,
        // to avoid ID collisions with joins.
        $query->select(sprintf('%s.*', $sample->getTable()));

        // Perform joins on related tables
        foreach ($joins as $table => $keys) {
            $query->leftJoin($table, $keys[0], $keys[1]);
        }

        // Additional constraints to be passed as "where" clauses
        $constraints = array_get($this->searchable, 'constraints', []);

        foreach ($constraints as $column => $constraint) {
            list($operator, $value) = $constraint;
            $query->where($column, $operator, $value);
        }

        // Collection to hold column names
        $collection = collect([]);

        $searchColumns->each(function ($searchColumn) use (&$collection) {
            if (ends_with($searchColumn, '*')) {
                // "Wildcard" searches like 'users.*'
                $table = explode('.', $searchColumn)[0];

                // Build a "smart" list of search columns
                $columns = collect($this->getConnection()->getSchemaBuilder()->getColumnListing($table))
                    ->reject(function ($column) {
                        return in_array($column, ['id', 'password', 'remember_token'])
                        || ends_with($column, '_id')
                        || ends_with($column, '_at')
                        || ends_with($column, '_on');
                    })->map(function ($column) use ($table) {
                    return sprintf('`%s`.`%s`', trim($table, '`'), $column);
                });

                $collection = $collection->merge($columns);
            } elseif (str_contains($searchColumn, ',')) {
                // Comma-separated columns; i.e. "users.first_name, last_name, email"
                list($table, $columns) = explode('.', $searchColumn);

                $columns = collect(preg_split('/,\s*/', $columns))
                    ->map(function ($column) use ($table) {
                        return sprintf('`%s`.`%s`', trim($table, '`'), trim($column));
                    });

                $collection = $collection->merge($columns);
            } else {
                // Explicit column name provided
                $collection = $collection->merge($searchColumn);
            }
        });

        // Prepare a CONCAT_WS statement
        $concatenated = $collection->implode(', ');

        // Search through all requested columns for each token
        foreach ($terms as $term) {
            $term = trim($this->getConnection()->getPdo()->quote($term), "'");
            $query->whereRaw("CONCAT_WS('', {$concatenated}) LIKE '%$term%'");
        }

        // Avoid duplicate records
        return $query->distinct();
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER BY Clauses
    |--------------------------------------------------------------------------
     */

    public function scopeAscending($query)
    {
        return $query->orderBy('id', 'asc');
    }

    public function scopeDescending($query)
    {
        return $query->orderBy('id', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Filters
    |--------------------------------------------------------------------------
     */

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
