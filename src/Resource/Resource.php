<?php

namespace LaravelDomainOriented\Resource;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class Resource extends JsonResource
{
    protected abstract function data(): array;

    protected function defaultData(): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'inactivated_at' => $this->inactivated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public function toArray($request): array
    {
        return array_merge($this->data(), $this->defaultData());
    }
}
