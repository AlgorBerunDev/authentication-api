<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\StatusCode;

class NotFoundToken extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'unauthorized',
            'status_code' => StatusCode::NOT_FOUND_TOKEN
        ], 403);
    }
}
