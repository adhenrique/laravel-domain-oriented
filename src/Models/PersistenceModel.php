<?php

namespace LaravelDomainOriented\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersistenceModel extends Model
{
    use SoftDeletes;
}
