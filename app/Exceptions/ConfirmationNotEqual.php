<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class ConfirmationNotEqual extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }

    public function render(){

        $result = array_merge([
            'description' => 'Confirmation not equal',
            'error' => ErrorCode::CONFIRMATION_NOT_EQUAL,
        ], $this->options);

        return response()->json($result, 400);
    }
}
