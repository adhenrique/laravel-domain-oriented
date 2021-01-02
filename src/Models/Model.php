<?php

namespace LaravelDomainOriented\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;
}
