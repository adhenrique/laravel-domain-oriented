<?php

namespace LaravelDomainOriented\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelDomainOriented\Exceptions\OperationIsNotAllowed;

class SearchModel extends Model
{
    public $incrementing = false;

    protected function performInsert(Builder $query): bool
    {
        throw new OperationIsNotAllowed();
    }

    protected function performUpdate(Builder $query): bool
    {
        throw new OperationIsNotAllowed();
    }

    protected function performDeleteOnModel()
    {
        throw new OperationIsNotAllowed();
    }

    protected function truncate()
    {
        throw new OperationIsNotAllowed();
    }
}
