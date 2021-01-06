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

    public function show(Request $request, int $id)
    {
        $request = $request->merge(['id' => $id]);
        $validatedData = $this->validateService->handle($request->all(), ValidateService::SHOW);

        $data = $this->searchService->findById($validatedData['id']);
        return new $this->resource($data);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $this->validateService->handle($request->all(), ValidateService::STORE);
        $id = $this->persistenceService->store($validatedData);
        return $this->response(['id' => $id]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request = $request->merge(['id' => $id]);
        $validatedData = $this->validateService->handle($request->all(), ValidateService::UPDATE);

        $isUpdated = $this->persistenceService->update($validatedData, $id);
        return $this->response(['isUpdated' => $isUpdated]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $request = $request->merge(['id' => $id]);
        $validatedData = $this->validateService->handle($request->all(), ValidateService::DESTROY);

        $isDeleted = $this->persistenceService->destroy($validatedData['id']);
        return $this->response(['isDeleted' => $isDeleted]);
    }
}
