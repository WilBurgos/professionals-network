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

        if($user){
            if(Crypt::decrypt($user->password) != $request->password){
                return response()->json([
                    'result' => false,
                    'reason' => 'Incorrect password'
                ], 401);
            }
        }else{
            return response()->json([
                'result' => false,
                'reason' => 'There is no email in our records'
            ], 401);
        }

        $accessToken = $user->createToken('CRIMSON-TOKEN')->accessToken;

        return response()->json([
            "result" => true,
            "message" => "Authenticated user",
            "token" => $accessToken
        ],200);
    }

    public function logout(){
        Auth()->user()->tokens->each(function($token, $key) {
            $token->delete();
        });

        return response()->json([
            "result" => true,
            "message" => "Successfully logged out"
        ],200);
    }
}
