<?php

namespace LaravelDomainOriented\Services;

use LaravelDomainOriented\Models\PersistenceModel;

class PersistenceService
{
    protected PersistenceModel $model;

    public function __construct(PersistenceModel $model)
    {
        $this->model = $model;
    }

    public function store(array $data)
    {
        $createdModel = $this->model->create($data);
        return $createdModel->id;
    }

    public function update(array $data, int $id)
    {
        $model = $this->model->findOrFail($id);

        foreach ($data as $field => $value) {
            $model->{$field} = $value;
        }

        $updatedModel = $model->save();
        return $updatedModel;
    }
}