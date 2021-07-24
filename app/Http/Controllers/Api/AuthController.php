<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\TestException;
use App\Exceptions\ValidatorException;
use Illuminate\Support\Facades\Validator;

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

    public function test(Request $request){
        // 'identity' => 'savion11@example.org',
        // 'password' => 'password1'


        throw new TestException(['message' => 'test']);

        $validator = Validator::make($request->all(), [
            'identity' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json(
                array_merge([
                    'errors' => $validator->messages(),
                    'message' => 'Validation error',
                    'statusCode' => 0
                ])
                , 400);
        }

        try {
            $users = User::attempt($request->all());
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'identity or password not condition',
                'errors' => [],
                'statusCode' => 0
            ], 403);
        }

        return response()->json([
            'users' => $users
        ]);
    }
}
