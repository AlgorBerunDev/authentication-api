<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\StatusCode;

class SessionLimitted extends Exception
{
    public function render(){
        return response()->json([
            'description' => 'Session limited',
            'status_code' => StatusCode::SESSION_LIMITTED
        ], 400);
    }
}
