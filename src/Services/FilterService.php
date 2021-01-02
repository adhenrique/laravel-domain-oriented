<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FilterService
{
    protected array $fields = ['id', 'name'];
    protected Builder $builder;

    // fixme - Is there a better way to do these conditionals?
    public function apply(Builder $builder, Request $request): Builder
    {
        $this->builder = $builder;
        foreach ($request->all() as $field => $value) {
            if ($field === 'or') {
                // todo - create a logic to this
            }
            if (in_array($field, $this->fields)) {
                if (method_exists($this, $field)) {
                    $this->builder = $this->$field($value);
                } else {
                    if (is_array($value)) {
                        if (Arr::has($value, 'operator') && Arr::has($value, 'value')) {
                            $this->builder->where($field, $value['operator'], $value['value']);
                        } else if (Arr::has($value, 'start')) {
                            $this->builder->whereBetween($field, $value);
                        } else {
                            $this->builder->whereIn($field, $value);
                        }
                    } else {
                        $this->builder->where($field, $value);
                    }
                }
            }
        }

        return $this->builder;
    }
}
