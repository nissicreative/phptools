<?php

namespace Nissi\Database;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilters
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Create a new QueryFilters instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Illuminate\Database\Query\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $methodName = camel_case($name);

            if (is_array($value) || strlen($value)) {
                $this->$methodName($value);
            } else {
                $this->$methodName();
            }
        }

        return $this->builder;
    }

    /**
     * Get all request filters data.
     *
     * @return Illuminate\Support\Collection
     */
    public function filters()
    {
        return collect($this->request->all())
            ->filter(function ($value, $name) {
                $methodName = camel_case($name);
                return method_exists($this, $methodName);
            });
    }

    /**
     * A list of all valid query parameter names being applied.
     *
     * @return Array
     */
    public function filterKeys($includeEmpty = false)
    {
        if ($includeEmpty) {
            return $this->filters()->keys();
        }

        return $this->filters()
            ->reject(function ($val) {
                return $val === '';
            })
            ->keys();
    }

}
