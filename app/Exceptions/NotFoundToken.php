<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class NotFoundToken extends Exception
{
    public function render() {
        return response()->json([
            'message' => 'unauthorized',
            'error' => ErrorCode::NOT_FOUND_TOKEN
        ], 403);
    }
}
