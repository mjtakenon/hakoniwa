<?php

namespace App\Http\Traits;
trait WebApi {
    function ok(array $data = [], int $status = 200, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function created(array $data = [], int $status = 201, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function noContent(array $data = [], int $status = 204, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function badRequest(array $data = ['code' => 'bad_request'], int $status = 400, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function forbidden(array $data = ['code' => 'forbidden'], int $status = 403, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function notFound(array $data = ['code' => 'not_found'], int $status = 404, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function internalServerError(array $data = ['code' => 'internal_server_error'], int $status = 500, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }

    function serviceUnavailable(array $data = ['code' => 'service_unavailable'], int $status = 503, array $headers = [], int $options = 0): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $status, $headers, $options);
    }
}
