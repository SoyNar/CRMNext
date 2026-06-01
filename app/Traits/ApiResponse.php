<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{

    public function respond(mixed $data, string $message = "success", int $statusCode = 200): mixed
    {
        $resource = $data->resource ?? null;

        if ($resource instanceof LengthAwarePaginator) {
            return response()->json([
                'data'         => $data,
                'current_page' => $resource->currentPage(),
                'last_page'    => $resource->lastPage(),
                'total'        => $resource->total(),
                'message'      => $message,
            ], $statusCode);
        }

        return response()->json([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $statusCode,
        ], $statusCode);
    }

}
