<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ValidatorException extends Exception
{
    private $messages;
    public function __construct(MessageBag $messages){
        // dd($messages);
        $this->messages = $messages;
    }
    public function render() {

        $errors = $this->messages->toArray();

        $result = [];
        foreach ($errors as $key => $value) {
            array_push($result, [
                'field' => $key,
                'messages' => $value
            ]);
        }
        return response()->json(
            array_merge([
                'errors' => $result,
                'description' => 'Validation error',
            ])
            , 400);
    }
}
