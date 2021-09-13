<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class Limitted extends Exception
{
    public function render(){
        return response()->json([
            'message' => 'Limited creating',
            'error' => ErrorCode::LIMITTED
        ], 400);
    }
}
