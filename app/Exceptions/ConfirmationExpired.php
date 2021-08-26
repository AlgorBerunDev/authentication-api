<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class ConfirmationExpired extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }

    public function render(){

        $result = array_merge([
            'description' => 'Confirmation expired',
            'error' => ErrorCode::CONFIRMATION_EXPIRED,
        ], $this->options);

        return response()->json($result, 400);
    }
}
