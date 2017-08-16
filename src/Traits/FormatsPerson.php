<?php

namespace Nissi\Traits;

trait FormatsPerson
{
    use FormatsName, FormatsPhone, FormatsAddress, HasAvatar, QueryScopes;

    /**
     * Collection returned in alphabetical order.
     */
    public function scopeAlphabetized($query)
    {
        return $query->orderBy('last_name')
            ->orderBy('first_name');
    }

    /**
     * Returns array of users, sorted alphabetically,
     * in $id => $name format.
     */
    public static function selectList()
    {
        return self::query()
            ->alphabetized()
            ->get()
            ->keyBy('id')
            ->map(function ($user) {
                return $user->name;
            });
    }
}
