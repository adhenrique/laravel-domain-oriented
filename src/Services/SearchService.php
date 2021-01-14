<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelDomainOriented\Models\SearchModel;

class SearchService
{
    protected SearchModel $model;
    protected FilterService $filterService;
    protected int $perPage = 15;

    public function __construct(SearchModel $model, FilterService $filterService)
    {
        $this->model = $model;
        $this->filterService = $filterService;
    }

    public function all(Request $request)
    {
        $builder = $this->beforeAll($this->model->query(), Auth::guard());

        $builder = $this->filterService->apply($builder, $request);
        $paginate = $request->get('paginate');

        $page = $paginate['page'] ?? 0;
        $perPage = $paginate['per_page'] ?? $this->perPage;

        if ($paginate) {
            return $builder
                ->paginate($perPage, [$this->model->getTable().".*"], 'page', $page);
        }
        return $builder->get();
    }

    public function findById(int $id)
    {
        $builder = $this->beforeFindById($this->model->query(), Auth::guard());

        return $builder->findOrFail($id);
    }

    public function getTableName(): string
    {
        return $this->model->getTable();
    }

    public function beforeAll(Builder $builder, Guard $auth): Builder
    {
        return $builder;
    }

    public function beforeFindById(Builder $builder, Guard $auth): Builder
    {
        return $builder;
    }
}
