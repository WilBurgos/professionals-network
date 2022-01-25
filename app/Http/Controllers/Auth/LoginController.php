<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        $user = User::where('email', $request->email)->first();

        if(Crypt::decrypt($user->password) != $request->password){
            return response()->json([
                'result' => false,
                'reason' => 'Incorrect password'
            ], 401);
        }

        $accessToken = $user->createToken('CRIMSON-TOKEN')->accessToken;

        return response()->json([
            "result" => true,
            "message" => "Authenticated user",
            "token" => $accessToken
        ],200);
    }
}
