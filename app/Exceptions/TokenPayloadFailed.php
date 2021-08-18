<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\StatusCode;

class TokenPayloadFailed extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'token payload failed',
            'error' => ErrorCode::TOKEN_PAYLOAD_FAILED
        ], 403);
    }
}
