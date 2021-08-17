<?php
namespace App\Exceptions;

class StatusCode
{
    const NOT_FOUND_OR_REMOVED=1;
    const NOT_FOUND_TOKEN=2;
    const PASSWORD_FAILED=3;
    const SESSION_LIMITED=4;
    const TOKEN_PAYLOAD_FAILED=5;
    const USER_NOT_FOUND=6;
    const VALIDATOR_EXCEPTION=7;
}


