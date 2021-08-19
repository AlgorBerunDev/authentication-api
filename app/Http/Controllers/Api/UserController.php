<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\JWT;
use Firebase\JWT\ExpiredException;
use App\Models\User;
use App\Models\Sessions;
use App\Exceptions\TestException;
use App\Exceptions\ValidatorException;
use App\Exceptions\UserNotFound;
use App\Exceptions\PasswordFailed;
use App\Exceptions\SessionLimitted;
use App\Exceptions\NotFoundOrRemoved;

class UserController extends Controller
{
    public function refresh(Request $request){

        $bearer_token = $request->input('refresh_token');

        if(!$bearer_token) {
            throw new NotFoundToken;
        }

        $decode_payload = JWT::decode($bearer_token);

        if(!$decode_payload) {
            throw new TokenPayloadFailed;
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
                'error' => 1
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'description' => $th->getMessage(),
                'error' => 2
            ], 400);
        }

        $new_tokens = Sessions::refreshToken($session);
        $user = User::find($session->user_id);

        return response()->json([
            'session' => $new_tokens,
            'user' => [
                'id' => $user->id,
                'identity' => $user->identity
            ]
        ]);
    }
    public function logout(Request $request){
        $bearer_token = $request->header('Authorization');

        if(!$bearer_token) {
            throw new NotFoundToken;
        }

        $decode_payload = JWT::decode($bearer_token);

        if(!$decode_payload) {
            throw new TokenPayloadFailed;
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
                'error' => 2
            ], 400);
        }
        $session->delete();
        return response()->json([
            'description' => "Logout successfully",
            'error' => 0
        ]);
    }
}
