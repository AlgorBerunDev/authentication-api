<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserNotFound extends Exception
{
    public function render(){
        return response()->json([
            'description' => 'User not found or removed in db',
        ], 400);
    }
}
