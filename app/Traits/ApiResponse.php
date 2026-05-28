<?php

namespace App\Traits;

trait ApiResponse
{

    public function respond(mixed $data, string $message = "success",int $statusCode = 200): mixed
    {

        return response()->json([
            'data' => $data,
            'message' => $message,
            'status_code' => $statusCode
        ] , $statusCode);

    }

}
