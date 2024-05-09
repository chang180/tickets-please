<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        // foreach($this->request->all() as $key => $value){
        //     if (method_exists($this, $key)) {
        //         $this->$key($value);
        //     }
        // }
        $this->filter($this->request->all());

        return $builder;
    }

    protected function sort($value)
    {
        $sortAttributes = explode(',', $value);
        foreach ($sortAttributes as $sortAttribute) {
            $sort = 'asc';
            if (substr($sortAttribute, 0, 1) == '-') {
                $sort = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }

            if (!in_array($sortAttribute, $this->sortable) && !array_key_exists($sortAttribute, $this->sortable)) {
                continue;
            }

            $columnName = $this->sortable[$sortAttribute] ?? $sortAttribute;

            $this->builder->orderBy($columnName, $sort);
        }
    }
}
