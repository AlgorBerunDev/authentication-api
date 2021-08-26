<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class ConfirmationSendLimited extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }
    public function render(){

        $result = array_merge([
            'description' => 'Confirmation send limited',
            'error' => ErrorCode::CONFIRMATION_SEND_LIMITED,
        ], $this->options);

        return response()->json($result, 400);
    }
}
