<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jsonwebtoken\JWT;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function test(Request $request) {


        return response()->json([
            'user' => Str::random(128),
        ]);
    }
}
