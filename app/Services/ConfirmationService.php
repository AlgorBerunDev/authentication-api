<?php

namespace App\Services;
use App\Services\SmsService\SmsHelperService;
use App\Services\SmsService\SmsService;
use App\Services\EmailService;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Mail;

class ConfirmationService
{
    const ALL=0;
    const ONLY_EMAIL=1;
    const ONLY_PHONE=2;
    const ONLY_APP=3;
    public function send($to, $code, $device = 0) {
        if($device == self::ALL) {
            $phone = SmsHelperService::clearPhone($to);
            if(SmsHelperService::isUzPhone($phone)) {
                $smsService = new SmsService();
                $smsService->sendConfirmationCode($phone, $code);
            }
            if(true) { // is email
                // Mail::to($to)->send(new SendConfirmationCode());
                Mail::to("algorberun@gmail.com")->send(new SendConfirmationCode());
            }

        }

        if($device == self::ONLY_EMAIL) {

        }

        if($device == self::ONLY_PHONE) {

        }

        if($device == self::ONLY_APP) {

        }
    }
}
