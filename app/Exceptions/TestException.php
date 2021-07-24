<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TestException extends Exception
{
    private $errors = [];

    public function __construct(array $errors)
    {
        return $this->errors = $errors;
    }

    public function render() {
        return response()->json([
            'errors' => $this->errors
        ], Response::HTTP_UNAUTHORIZED);
    }
}
