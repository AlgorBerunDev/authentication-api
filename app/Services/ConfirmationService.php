<?php

namespace App\Services;
use App\Models\User;
use App\Services\SmsService\SmsHelperService;
use App\Services\SmsService\SmsService;
use App\Services\EmailService;
use App\Services\PushNotificationService;
use App\Mail\SendConfirmationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ConfirmationService
{
    const ALL=0;
    const ONLY_EMAIL=1;
    const ONLY_PHONE=2;
    const ONLY_APP=3;
    public static function sendCode($options) {
        $user = User::find($options['user_id']);
        $to = $user->identity;
        $code = $options['code'];
        $device = $options['send_to_device'];

        if($device == self::ALL) {
            $phone = SmsHelperService::clearPhone($to);
            if(SmsHelperService::isUzPhone($phone)) {
                $smsService = new SmsService();
                $smsService->sendConfirmationCode($phone, $code);
            }
            $validator = Validator::make(['identity' => $to], [
                'identity' => 'email:rfc,dns',
            ]);

            if(!$validator->fails()){
                Mail::to("algorberun@gmail.com")->send(new SendConfirmationCode($code));
            }
        }

        if($device == self::ONLY_EMAIL) {
            $validator = Validator::make(['identity' => $to], [
                'identity' => 'email:rfc,dns',
            ]);

            if(!$validator->fails()){
                Mail::to("algorberun@gmail.com")->send(new SendConfirmationCode($code));
            }
        }

        if($device == self::ONLY_PHONE) {
            $phone = SmsHelperService::clearPhone($to);
            if(SmsHelperService::isUzPhone($phone)) {
                $smsService = new SmsService();
                $smsService->sendConfirmationCode($phone, $code);
            }
        }

        if($device == self::ONLY_APP) {
            //TODO: push notification ulangandan keyin yoziladi bu qismi
        }
    }
}