<?php
namespace Nissi\Traits;

use Illuminate\Support\Facades\DB;

trait HasEnumColumns
{
    /**
     * Retrieves the acceptable enum fields for a column
     *
     * @param  string  $column Column name
     * @return array
     */
    public function getEnumValues($column)
    {
        // Pulls column string from DB
        $enumStr = DB::select(DB::raw('SHOW COLUMNS FROM ' . $this->getTable() . ' WHERE Field = "' . $column . '"'))[0]->Type;

        // Parse string
        preg_match_all("/'([^']+)'/", $enumStr, $matches);

        // Return matches
        return isset($matches[1]) ? array_reflect($matches[1]) : [];
    }

}
