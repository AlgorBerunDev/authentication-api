<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ErrorCode;
use Illuminate\Http\Request;

class UserNotFound extends Exception
{
    public function render($request){

        return response()->json([
            'description' => 'User not found or removed in db',
            'error' => ErrorCode::USER_NOT_FOUND,
            'o' => trans('messages.welcome')
        ], 400);
    }
}
