<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\StatusCode;

class PasswordFailed extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'Identity or password incorrect',
            'status_code' => StatusCode::PASSWORD_FAILED
        ], 400);
    }
}
