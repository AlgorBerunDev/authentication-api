<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class LoginBlocked extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }
    public function render(){

        $result = array_merge([
            'message' => 'Login blocked',
            'error' => ErrorCode::LOGIN_BLOCKED,
        ], $this->options);

        return response()->json($result, 400);
    }
}
