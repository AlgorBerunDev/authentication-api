<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Sessions;
use App\Exceptions\AccessDenied;
use App\Exceptions\ValidatorException;


class SessionController extends Controller
{
    public function getSessions(Request $request){
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;
        $sessions = Sessions::where('user_id', $user_id)->get([
            "id",
            "confirmation_blocked_to",
            "is_activated",
            "owner",
            "created_at",
            "updated_at"
        ]);

        return response()->json([
            'data' => $sessions,
            'error' => 0
        ]);
    }
    public function removeSessions(Request $request){
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $session = Sessions::find($session_id);
        if(!$session->owner) {
            throw new AccessDenied();
        }

        $validator = Validator::make($request->all(), [
            'sessions' => 'required|array',
            'sessions.*' => 'required|integer'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $session_ids = array_filter(
            $request->input('sessions'),
            function($v) use ($session_id) {return $v != $session_id;}
        );
        $session_ids = array_values($session_ids);


        $n = Sessions::whereIn('id', $session_ids)->delete();
        // dd($session_ids);
        return response()->json([
            'n' => $n,
            '$session_ids' => $session_ids,
            'error' => 0
        ]);
    }
    public function updateFcmToken(Request $request) {
        $session_id = $request->get('payload')->session_id;

        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|min:10|max:512',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $n = Sessions::where('id', $session_id)
        ->update(['fcm_token' => $request->input('fcm_token')]);

        return response()->json([
            'n' => $n,
            'error' => 0
        ]);
    }
}
