<?php
namespace Nissi\Traits;

use Carbon\Carbon;
use DB;

trait QueryScopes
{
    public function scopeActive($query)
    {
        return $query->where('active', 1);
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

}
