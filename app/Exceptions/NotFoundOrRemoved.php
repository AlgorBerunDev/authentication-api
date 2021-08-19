<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ErrorCode;

class NotFoundOrRemoved extends Exception
{
    private $attribute = "";

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function render() {
        return response()->json([
            'description' => "$this->attribute not found or removed in db",
            'error' => ErrorCode::NOT_FOUND_OR_REMOVED
        ], Response::HTTP_UNAUTHORIZED);
    }
}