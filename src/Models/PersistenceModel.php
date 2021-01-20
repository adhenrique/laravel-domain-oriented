<?php

namespace LaravelDomainOriented\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersistenceModel extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        // todo - add fields here
    ];
}
