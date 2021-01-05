<?php

namespace LaravelDomainOriented\Services;

use Illuminate\Http\Request;
use LaravelDomainOriented\Models\Model;

class SearchService
{
    protected Model $model;
    protected FilterService $filterService;
    protected int $perPage = 15;

    public function __construct(Model $model, FilterService $filterService)
    {
        $this->model = $model;
        $this->filterService = $filterService;
    }

    public function all(Request $request)
    {
        $builder = $this->filterService->apply($this->model->query(), $request);
        $paginate = $request->get('paginate');

        $page = $paginate['page'] ?? 0;
        $perPage = $paginate['per_page'] ?? $this->perPage;

        if ($paginate) {
            return $builder
                ->paginate($perPage, [$this->model->getTable().".*"], 'page', $page);
        }
        return $builder->get();
    }
}
