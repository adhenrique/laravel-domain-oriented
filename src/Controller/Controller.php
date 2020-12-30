<?php


namespace LaravelDomainOriented\Controller;


use Illuminate\Http\JsonResponse;

class Controller extends \Illuminate\Routing\Controller
{
    public function response(array $data = [], $status = 200): JsonResponse
    {
        return response()->json(['data' => $data], $status);
    }

    public function index(): JsonResponse
    {
        return $this->response([]);
    }
}
