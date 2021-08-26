<?php
namespace App\Exceptions;

class ErrorCode
{
    const NOT_FOUND_OR_REMOVED=1;
    const NOT_FOUND_TOKEN=2;
    const PASSWORD_FAILED=3;
    const SESSION_LIMITED=4;
    const TOKEN_PAYLOAD_FAILED=5;
    const USER_NOT_FOUND=6;
    const VALIDATOR_EXCEPTION=7;
    const CONFIRMATION_SEND_LIMITED=8; // o'rnatilgan vaqt mobaynida ortiqcha CONFIRMATION yuborilsa bloklangandagi xatolik
    const CONFIRMATION_EXPIRED=9; // CODEni tekshirish vaqti o'tib ketgan
    const CONFIRMATION_ATTEMPT_LIMITED=10; // CODEni ketma-ket xato tekshirish orqali limitdan o'tib ketgandagi xatolik
    const CONFIRMATION_NOT_EQUAL=11; // CODE noto'g'ri bo'lgandagi xatolik
    const LOGIN_BLOCKED=12;
    const JWT_EXPIRED=13;
    const JWT_FAILED=14;
}


