<?php

namespace App\Exceptions;

use Exception;

class SessionLimitted extends Exception
{
    public function render(){
        return response()->json([
            'description' => 'Session limited',
        ], 400);
    }
}
