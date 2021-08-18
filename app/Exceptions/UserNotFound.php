<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ErrorCode;

class UserNotFound extends Exception
{
    public function render(){
        return response()->json([
            'description' => 'User not found or removed in db',
            'error' => ErrorCode::USER_NOT_FOUND
        ], 400);
    }
}
