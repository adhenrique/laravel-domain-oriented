<?php

namespace LaravelDomainOriented\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use LaravelDomainOriented\Services\PersistenceService;
use LaravelDomainOriented\Services\SearchService;
use LaravelDomainOriented\Services\ValidateService;

class Controller extends BaseController
{
    protected SearchService $searchService;

    protected PersistenceService $persistenceService;

    protected ValidateService $validateService;

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

    // todo - validate $id parameter
    public function show(int $id)
    {
        $data = $this->searchService->findById($id);
        return new $this->resource($data);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateService->handle($request->all());
        $id = $this->persistenceService->store($data);
        return $this->response(['id' => $id]);
    }

    // todo - validate $id parameter
    public function update(Request $request, $id): JsonResponse
    {
        $data = $this->validateService->handle($request->all());
        $isUpdated = $this->persistenceService->update($data, $id);
        return $this->response(['isUpdated' => $isUpdated]);
    }

    // todo - validate $id parameter
    public function destroy(int $id): JsonResponse
    {
        $isDeleted = $this->persistenceService->destroy($id);
        return $this->response(['isDeleted' => $isDeleted]);
    }
}
