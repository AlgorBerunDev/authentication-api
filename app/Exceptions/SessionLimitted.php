<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class SessionLimitted extends Exception
{
    public function render(){
        return response()->json([
            'message' => 'Session limited',
            'error' => ErrorCode::SESSION_LIMITED
        ], 400);
    }
}
