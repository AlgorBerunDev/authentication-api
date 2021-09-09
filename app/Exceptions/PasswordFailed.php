<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\ErrorCode;

class PasswordFailed extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }

    public function render() {

        $result = array_merge([
            'message' => 'Identity or password incorrect',
            'error' => ErrorCode::PASSWORD_FAILED
        ], $this->options);

        return response()->json($result, 400);
    }
}
