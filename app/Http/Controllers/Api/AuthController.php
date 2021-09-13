<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Sessions;
use App\Models\Confirmation;
use App\Models\LoginAttempt;
use App\Services\ConfirmationService;
use App\Services\DateIntervalService;
use App\Exceptions\ValidatorException;
use App\Exceptions\UserNotFound;
use App\Exceptions\PasswordFailed;
use App\Exceptions\SessionLimitted;
use App\Exceptions\ConfirmationAttemptLimited;
use App\Exceptions\ConfirmationExpired;
use App\Exceptions\ConfirmationNotEqual;
use App\Exceptions\ConfirmationSendLimited;
use App\Exceptions\LoginBlocked;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function limits(Request $request) {
        return response()->json([
            'confirmation' => [
                'send_limit' => config('services.confirmation.send_limit'),
                'send_time' => DateIntervalService::toSecond(config('services.confirmation.send_time')),
                'send_block' => DateIntervalService::toSecond(config('services.confirmation.send_block')),
                'max_attempts' => config('services.confirmation.max_attempts'),
                'validity_period' => DateIntervalService::toSecond(config('services.confirmation.validity_period')),

                'user_send_limit' => config('services.confirmation.user_send_limit'),
                'user_send_time' => DateIntervalService::toSecond(config('services.confirmation.user_send_time')),
                'user_send_block' => DateIntervalService::toSecond(config('services.confirmation.user_send_block')),
            ],
            'login_attempt' => [
                'limit' => config('services.login_attempt.limit'),
                'period' => DateIntervalService::toSecond(config('services.login_attempt.period')),
                'block_duration' => DateIntervalService::toSecond(config('services.login_attempt.block_duration'))
            ],
            'error' => 0
        ]);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'identity' => 'required|string|max:100|unique:users',
            'username' => 'required|string|max:100|min:2',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'error' => 0
        ], 200);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'identity' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $user = User::getByIdentity($request->input('identity'));

        if(!$user) {
            throw new UserNotFound;
        }

        $now = new \DateTime();
        $login_blocked_to = new \DateTime($user->login_blocked_to);

        if($login_blocked_to > $now) {
            throw new LoginBlocked(['login_blocked_to' => $login_blocked_to->format(\DateTime::ATOM)]);
        }



        if(!Hash::check($request->input('password'), $user->password)) {

            LoginAttempt::create([
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'device_info' => 'feature'
            ]);

            $date = new \DateTime();
            $date->sub(new \DateInterval(config('services.login_attempt.period')));

            $loginAttempCount = LoginAttempt::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
                ->where('user_id', $user->id)
                ->where('status', '!=', LoginAttempt::CANCELED)
                ->count(); // the number of confirmation codes sent in the last send_time

            if($loginAttempCount >= config('services.login_attempt.limit')) {
                $login_blocked_to = new \DateTime();
                $login_blocked_to->add(new \DateInterval(config('services.login_attempt.block_duration')));

                $user->login_blocked_to = $login_blocked_to;
                $user->save();

                LoginAttempt::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
                ->where('user_id', $user->id)
                ->where('status', '!=', LoginAttempt::CANCELED)
                ->update([
                    'status' => LoginAttempt::CANCELED
                ]);
            }
            throw new PasswordFailed([
                'login_attempt_count' => $loginAttempCount
            ]);
        }
        if(!Sessions::checkLimit($user)) {
            throw new SessionLimitted;
        }

        $session = Sessions::createSession($user);

        return response()->json([
            'session' => $session,
            'user' => [
                'id' => $user->id,
                'identity' => $user->identity,
                'username' => $user->username
            ],
            'error' => 0
        ]);
    }
    public function sendConfirmation(Request $request) {

        // send_to qaysi devicelarga confirmation codeni yuborish kerakligini aytadi
        $validator = Validator::make($request->all(), [
            'send_to_device' => 'required|integer|max:10',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;
        $code = random_int(100000, 999999);

        $session = Sessions::find($session_id);
        $confirmation_blocked_to = new \DateTime($session->confirmation_blocked_to);
        $now = new \DateTime();

        if($confirmation_blocked_to > $now) {

            throw new  ConfirmationSendLimited([
                'confirmation_blocked_to' => $confirmation_blocked_to->format(\DateTime::ATOM),
            ]);
        }

        $date = new \DateTime();
        $date->sub(new \DateInterval(config('services.confirmation.send_time')));
        $count_sended_confirmation = Confirmation::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
            ->where('session_id', $session_id)
            ->where('status', '!=', Confirmation::BLOCKED)
            ->where('status', '!=', Confirmation::USER_BLOCKED)
            ->count(); // the number of confirmation codes sent in the last send_time
        $session_count_sended_confirmation = $count_sended_confirmation;

        if($count_sended_confirmation >= config('services.confirmation.send_limit')) { // FOR SESSION CHECK CONFIRMATION SENDING LIMITS
            $new_confirmation_blocked_to = new \DateTime();
            $new_confirmation_blocked_to->add(new \DateInterval(config('services.confirmation.send_block'))); // blocking the sending of the verification code to the customer until then

            $session->confirmation_blocked_to = $new_confirmation_blocked_to;
            $session->save();

            Confirmation::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
            ->where('session_id', $session_id)
            ->where('status', '!=', Confirmation::CREATED)
            ->update([
                'status' => Confirmation::BLOCKED
            ]); // counting the confirmation codes sent in the last 1 hour, we put them in the block so that the sending of the confirmation code is calculated
                //  correctly after leaving the block, in fact, they could be deleted, but may be needed to calculate the total number of SMS sent

            throw new  ConfirmationSendLimited([
                'confirmation_blocked_to' => $new_confirmation_blocked_to->format(\DateTime::ATOM),
            ]);
        }


        $date = new \DateTime();
        $date->sub(new \DateInterval(config('services.confirmation.user_send_time')));
        $count_sended_confirmation = Confirmation::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
            ->where('status', '!=', Confirmation::USER_BLOCKED)
            ->where('user_id', $user_id)
            ->count(); // the number of confirmation codes sent in the last send_time

        if($count_sended_confirmation >= config('services.confirmation.user_send_limit')) { // FOR USER CHECK CONFIRMATION SENDING LIMITS
            $new_confirmation_blocked_to = new \DateTime();
            $new_confirmation_blocked_to->add(new \DateInterval(config('services.confirmation.user_send_block'))); // blocking the sending of the verification code to the customer until then

            $session->confirmation_blocked_to = $new_confirmation_blocked_to;
            $session->save();

            User::where('id', $user_id)
            ->update([
                'confirmation_blocked_to' => $new_confirmation_blocked_to
            ]);

            Confirmation::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
            ->where('session_id', $session_id)
            ->where('status', '!=', Confirmation::CREATED)
            ->update([
                'status' => Confirmation::USER_BLOCKED
            ]); // counting the confirmation codes sent in the last 1 hour, we put them in the block so that the sending of the confirmation code is calculated
                //  correctly after leaving the block, in fact, they could be deleted, but may be needed to calculate the total number of SMS sent

            throw new  ConfirmationSendLimited([
                'confirmation_blocked_to' => $new_confirmation_blocked_to->format(\DateTime::ATOM),
            ]);
        }

        $sended_to = ConfirmationService::sendCode([
            'session_id' => $session_id,
            'user_id' => $user_id,
            'code' => $code,
            'send_to_device' => $request->input('send_to_device')
        ]);

        Confirmation::where('status', Confirmation::CREATED)
        ->where('session_id', $session_id)
        ->update([
            'status' => Confirmation::CANCELED
        ]);

        $confirmation = Confirmation::create([
            'session_id' => $session_id,
            'user_id' => $user_id,
            'code' => $code
        ]);

        return response()->json([
            'message' => 'A confirmation code has been sent to the service required to send it to the customer, try again if no message is received.',
            'sended_to_services' => $sended_to,
            'last_time_sended_count' => $session_count_sended_confirmation + 1,
            'user_last_time_sended_count' => $count_sended_confirmation + 1,
            'created_at' => $confirmation->created_at,
            'error' => 0
        ]);
    }
    public function confirmation(Request $request){

        $validator = Validator::make($request->all(), [
            'code' => 'required|integer|min:100000|max:999999',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $date = new \DateTime();
        $date->sub(new \DateInterval(config('services.confirmation.validity_period')));
        $confirmation = Confirmation::where('created_at', '>=', $date->format('Y-m-d H:i:s'))
            ->where('session_id', $session_id)
            ->where('status', Confirmation::CREATED)
            ->first();

        if(!$confirmation) {

            Confirmation::where('session_id', $session_id)
            ->where('status', Confirmation::CREATED)
            ->update([
                'status' => Confirmation::EXPIRED
            ]);

            throw new ConfirmationExpired();
        }

        if($confirmation->number_of_attempts >= config('services.confirmation.max_attempts')) {

            Confirmation::where('session_id', $session_id)
            ->where('status', Confirmation::CREATED)
            ->update([
                'status' => Confirmation::BLOCKED
            ]);

            throw new ConfirmationAttemptLimited();

        }

        if($confirmation->code != $request->input('code')) {

            $confirmation->number_of_attempts = $confirmation->number_of_attempts + 1;
            $confirmation->save();

            throw new ConfirmationNotEqual([
                'number_of_attempts' => $confirmation->number_of_attempts,
                'confirmation_created_at' => $confirmation->created_at,
            ]);
        }

        $confirmation->update([
            'status' => Confirmation::VERIFIED
        ]);

        $session_owner = Sessions::where('owner', true)->first();
        $session = Sessions::find($session_id);

        if(!$session_owner)
            $session->owner = true;

        $session->is_activated = true;
        $session->save();

        //TODO: confirmation success bogandan keyin qaytadan confirmation code kelganda yana qaytadan bolyapti shuni oldini olib qo'yish kerak
        return response()->json([
            'message' => 'Confirmed',
            'session' => [
                'id' => $session->id,
                'user_id' => $session->user_id,
                'is_activated' => $session->is_activated,
                'owner' => $session->owner, //TODO: keyinchalik role berilishi kerak kop rolelarni aniqlash uchun
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at
            ],
            'error' => 0
        ]);
    }
}
