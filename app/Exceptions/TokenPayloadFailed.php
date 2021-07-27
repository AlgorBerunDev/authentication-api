<?php

namespace App\Exceptions;

use Exception;

class TokenPayloadFailed extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'token payload failed'
        ], 403);
    }
}
