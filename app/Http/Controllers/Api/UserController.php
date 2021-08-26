<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Services\JWT;
use Firebase\JWT\ExpiredException;
use App\Models\User;
use App\Models\Sessions;
use App\Exceptions\TestException;
use App\Exceptions\TokenPayloadFailed;
use App\Exceptions\ValidatorException;
use App\Exceptions\UserNotFound;
use App\Exceptions\PasswordFailed;
use App\Exceptions\SessionLimitted;
use App\Exceptions\NotFoundOrRemoved;
use App\Exceptions\ErrorCode;

class UserController extends Controller
{
    public function refresh(Request $request){

        $bearer_token = $request->input('refresh_token');

        if(!$bearer_token) {
            throw new NotFoundToken;
        }

        $decode_payload = [];
        try {
            $decode_payload = JWT::decode($bearer_token);
        } catch (\Throwable $th) {
            throw new TokenPayloadFailed;
        } finally {
            if(!$decode_payload) throw new TokenPayloadFailed;
        }

        $session = Sessions::where('id', $decode_payload->session_id)->first();

        if(!$session) {
            throw new NotFoundOrRemoved('Session');
        }

        $exploded_auth_header = explode(" ", $bearer_token);
        $token = $exploded_auth_header[1];
        $payload = array();


        try {
            $payload = JWT::validate($token, $session->refreshKey);
        } catch (ExpiredException $th) {
            $session->delete();
            return response()->json([
                'description' => $th->getMessage(),
                'error' => ErrorCode::JWT_EXPIRED
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'description' => $th->getMessage(),
                'error' => ErrorCode::JWT_FAILED
            ], 400);
        }

        $new_tokens = Sessions::refreshToken($session);
        $user = User::find($session->user_id);

        return response()->json([
            'session' => $new_tokens,
            'user' => [
                'id' => $user->id,
                'identity' => $user->identity,
                'username' => $user->username,
            ],
            'error' => 0
        ]);
    }

    public function logout(Request $request){
        $bearer_token = $request->header('Authorization');

        if(!$bearer_token) {
            throw new NotFoundToken;
        }

        $decode_payload = [];
        try {
            $decode_payload = JWT::decode($bearer_token);
        } catch (\Throwable $th) {
            throw new TokenPayloadFailed;
        } finally {
            if(!$decode_payload) throw new TokenPayloadFailed;
        }

        $session = Sessions::where('id', $decode_payload->session_id)->first();

        if(!$session) {
            throw new NotFoundOrRemoved('Session');
        }

        $exploded_auth_header = explode(" ", $bearer_token);
        $token = $exploded_auth_header[1];
        $payload = array();


        try {
            $payload = JWT::validate($token, $session->refreshKey);
        } catch (ExpiredException $th) {
            $session->delete();
            return response()->json([
                'description' => "Logout successfully",
                'error' => 0
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'description' => $th->getMessage(),
                'error' => ErrorCode::JWT_FAILED
            ], 400);
        }
        $session->delete();
        return response()->json([
            'description' => "Logout successfully",
            'error' => 0
        ]);
    }

    public function profile(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;
        $user = User::find($user_id)->first([
            "id",
            "identity",
            "username",
            "is_blocked",
            "session_max_count",
            "super_session_max_count",
            "confirmation_blocked_to",
            "login_blocked_to",
            "created_at",
            "updated_at"
        ]);
        $session = Sessions::find($session_id)->first([
            "id",
            "confirmation_blocked_to",
            "is_activated",
            "created_at",
            "updated_at"
        ]);
        return response()->json([
            'session' => $session,
            'user' => $user,
            'error' => 0
        ]);
    }

    public function changeSessionMaxCount(Request $request) {

        $user_id = $request->get('payload')->user_id;
        $user = User::find($user_id)->first([
            "id",
            "identity",
            "username",
            "is_blocked",
            "session_max_count",
            "super_session_max_count",
            "confirmation_blocked_to",
            "login_blocked_to",
            "created_at",
            "updated_at"
        ]);

        $super_session_max_count = $user->super_session_max_count;

        // send_to qaysi devicelarga confirmation codeni yuborish kerakligini aytadi
        $validator = Validator::make($request->all(), [
            'session_max_count' => "required|integer|min:2|max:$super_session_max_count",
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $user->session_max_count = $request->input("session_max_count");
        $user->save();

        return response()->json([
            'user' => $user,
            'error' => 0
        ]);
    }
}
