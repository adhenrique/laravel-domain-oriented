<?php

namespace LaravelDomainOriented\Exceptions;

use RuntimeException;

class OperationIsNotAllowed extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("This operation is not allowed for this model");
    }
}
