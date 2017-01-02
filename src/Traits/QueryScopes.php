<?php
namespace Nissi\Traits;

use DB;
use Carbon\Carbon;

trait QueryScopes
{
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

    public function scopeRandom($query)
    {
        return $query->orderBy(DB::raw('RAND()'));
    }

    public function scopeLatest($query)
    {
        return $query->orderBy($this::CREATED_AT, 'desc');
    }

    public function scopeEarliest($query)
    {
        return $query->orderBy($this::CREATED_AT, 'asc');
    }

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

    public function scopeofUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, $q)
    {
        // Query string
        $q = trim($q);

        if (empty($q)) {
            return $query;
        }

        // Sample record so we can get table info
        $sample = static::first();

        // Search all columns unless a `searchable` array
        // is defined in the model
        $allColumns = array_keys($sample->toArray());

        $cols  = array_get($this->searchable, 'columns', $allColumns);
        $joins = array_get($this->searchable, 'joins', []);
        $terms = preg_split('~\W+~', $q);

        // Scope SELECT clause to the current model's table,
        // to avoid ID collisions with joins.
        $query->select(sprintf('%s.*', $sample->getTable()));

        // Perform joins on related tables
        foreach ($joins as $table => $keys) {
            $query->join($table, $keys[0], $keys[1]);
        }

        // Search through each column for entire query
        // or individual terms
        foreach ($cols as $col) {
            $query->orWhere($col, 'LIKE', "%$q%");

            foreach ($terms as $term) {
                $query->orWhere($col, 'LIKE', "%$term%");
            }
        }

        return $query;
    }

    public function scopeSorted($query, $direction = 'asc')
    {
        return $query->orderBy('position', $direction);
    }
}
