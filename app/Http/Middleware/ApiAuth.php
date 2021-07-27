<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sessions;
use App\Services\JWT;
use App\Exceptions\NotFoundToken;
use App\Exceptions\TokenPayloadFailed;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

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

        $decode_payload = JWT::decode($bearer_token);

        if(!$decode_payload) {
            throw new TokenPayloadFailed;
        }

        $session = Sessions::where('id', $decode_payload->session_id)->first();
        $exploded_auth_header = explode(" ", $bearer_token);
        $token = $exploded_auth_header[1];
        $payload = array();

        try {
            $payload = JWT::validate($token, $session->secretKey);
        } catch (\Throwable $th) {
            return response()->json(['description' => $th->getMessage()], 400);
        }

        return $next($request);
    }
}
