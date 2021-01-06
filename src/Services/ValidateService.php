<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Support\Facades\Validator;

class ValidateService
{
    protected array $rules = [
        'name' => 'required|string'
    ];

    public function handle(array $data): array
    {
        return Validator::validate($data, $this->rules);
    }
}
