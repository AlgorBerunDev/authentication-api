<?php

namespace App\Exceptions;

use Exception;

class NotFoundToken extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'unauthorized'
        ], 403);
    }
}
