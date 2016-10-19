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
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeEarliest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function scopeCreatedSince($query, $dt)
    {
        return $query->where('created_at', '>=', Carbon::parse($dt));
    }

    public function scopeCreatedBefore($query, $dt)
    {
        return $query->where('created_at', '<', Carbon::parse($dt));
    }

    public function scopeUpdatedSince($query, $dt)
    {
        return $query->where('updated_at', '>=', Carbon::parse($dt));
    }

    public function scopeUpdatedBefore($query, $dt)
    {
        return $query->where('updated_at', '<', Carbon::parse($dt));
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
