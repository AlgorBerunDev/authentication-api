<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class PasswordFailed extends Exception
{
    public function render() {
        return response()->json([
            'status' => 1,
            'error_note' => 'Identity or password incorrect'
        ], Response);
    }
}
