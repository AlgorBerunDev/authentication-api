<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Sessions;
use App\Exceptions\TestException;
use App\Exceptions\ValidatorException;
use App\Exceptions\UserNotFound;
use App\Exceptions\PasswordFailed;
use App\Exceptions\SessionLimitted;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
            'description' => 'User successfully registered',
            'user' => $user
        ], 201);
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

        if(!Hash::check($request->input('password'), $user->password)) {
            throw new PasswordFailed;
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
                // 'username' => $user->$username
            ]
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

        $limit_confirmation = User::getLimitConfirmation($request->get('payload')->user_id);


        return response()->json([
            'user_id' => $limit_confirmation,
            'send_to_device' => $request->input('send_to_device')
        ]);
    }
    public function confirmation(){}
}
