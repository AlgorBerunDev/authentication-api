<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PasswordFailed extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'Identity or password incorrect'
        ], 400);
    }
}
