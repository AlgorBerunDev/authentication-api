<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserNotFound extends Exception
{
    public function render(){
        return response()->json([
            'status' => '2',
            'error_note' => 'User not found or removed in db',
        ]);
    }
}
