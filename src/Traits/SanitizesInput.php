<?php

namespace Nissi\Traits;

trait SanitizesInput
{
    /**
     * Santize an input array.
     *
     * Trims non-array values, converts empty strings to null,
     * and attempts to convert date-like strings to a Carbon instance.
     *
     * @param  array $inputArray i.e. $request->all()
     * @return array
     */
    protected function sanitizeInput(array $inputArray)
    {
        return collect($inputArray)
            ->map(function ($value) {
                if (is_array($value)) {
                    return true;
                }

                $trimmed = trim($value);

                if (strlen($trimmed) > 4 && $date = make_date($trimmed)) {
                    return $date;
                }

                return $trimmed ?: null;
            })
            ->toArray();
    }

}
