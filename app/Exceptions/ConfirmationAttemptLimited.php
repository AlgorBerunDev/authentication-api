<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ErrorCode;

class ConfirmationAttemptLimited extends Exception
{
    private $options;

    public function __construct($options = []){
        $this->options = $options;
    }

    public function render(){
        $result = array_merge([
            'message' => 'Confirmation attempt limited',
            'error' => ErrorCode::CONFIRMATION_ATTEMPT_LIMITED,
        ], $this->options);

        return response()->json($result, 400);
    }
}
