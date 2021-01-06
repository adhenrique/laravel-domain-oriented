<?php

namespace LaravelDomainOriented\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use LaravelDomainOriented\Requests\StoreRequest;
use LaravelDomainOriented\Services\PersistenceService;
use LaravelDomainOriented\Services\SearchService;

class Controller extends BaseController
{
    protected SearchService $searchService;

    protected PersistenceService $persistenceService;

    protected StoreRequest $storeRequest;

    protected $resource;

    public function response(array $data = [], $status = 200): JsonResponse
    {
        return response()->json(['data' => $data], $status);
    }

    public function index(Request $request)
    {
        $data = $this->searchService->all($request);
        return $this->resource::collection($data);
    }

    public function show($id)
    {
        $data = $this->searchService->findById($id);
        return new $this->resource($data);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $id = $this->persistenceService->store($data);
        return $this->response(['id' => $id]);
    }
}
