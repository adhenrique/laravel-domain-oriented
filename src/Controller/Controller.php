<?php

namespace LaravelDomainOriented\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use LaravelDomainOriented\Services\SearchService;

class Controller extends BaseController
{
    protected SearchService $searchService;

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
}
