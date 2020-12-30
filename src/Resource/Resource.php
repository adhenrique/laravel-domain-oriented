<?php

namespace LaravelDomainOriented\Resource;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    protected function data(): array {
        return [];
    }

    protected function defaultData(): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'inactivated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public function toArray($request): array
    {
        return array_merge($this->data(), $this->defaultData());
    }
}
