<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ErrorCode;

class PasswordFailed extends Exception
{
    public function render() {
        return response()->json([
            'description' => 'Identity or password incorrect',
            'error' => ErrorCode::PASSWORD_FAILED
        ], 400);
    }
}
