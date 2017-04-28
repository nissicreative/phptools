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
    protected function sanitizeInput(array $inputArray, $dates = [])
    {
        return collect($inputArray)
            ->map(function ($value, $key) use ($dates) {
                if (is_array($value)) {
                    return true;
                }

                $trimmed = trim($value);

                if (in_array($key, $dates) && $date = make_date($trimmed)) {
                    return $date;
                }

                return $trimmed ?: null;
            })
            ->toArray();
    }

}
