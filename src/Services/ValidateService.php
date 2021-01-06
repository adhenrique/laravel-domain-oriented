<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Support\Facades\Validator;

class ValidateService
{
    const SHOW = 'show';
    const STORE = 'store';
    const UPDATE = 'update';
    const DESTROY = 'destroy';

    protected array $rules = [
        'name' => 'required|string'
    ];

    public function handle(array $data, string $action = null): array
    {
        return Validator::validate($data, $this->getRules($action));
    }

    private function getRules(string $action = null): array
    {
        $rules = $this->rules;

        if (isset($this->rules[$action])) {
            $rules = $this->rules[$action];
        }

        return $rules;
    }
}
