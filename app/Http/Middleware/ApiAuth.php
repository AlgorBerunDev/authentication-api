<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sessions;
use App\Services\JWT;
use App\Exceptions\NotFoundToken;
use App\Exceptions\TokenPayloadFailed;
use App\Exceptions\NotFoundOrRemoved;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\Exceptions\ErrorCode;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
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
            $payload = JWT::validate($token, $session->secretKey);
        } catch (ExpiredException $th) {

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


        $request->merge([
            'payload' => $payload
        ]);

        return $next($request);
    }
}
