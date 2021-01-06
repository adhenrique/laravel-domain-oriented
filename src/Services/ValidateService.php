<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class ValidateService
{
    protected static array $rules = [
        'name' => 'required|string'
    ];

    public static function handle(array $data): array
    {
        return Validator::validate($data, self::$rules);
    }
}
