<?php

namespace LaravelDomainOriented\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
