<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\Countries;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request){
        DB::beginTransaction();
        try {
            $user               = new User;
            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->password     = Crypt::encrypt($request->password);
            $user->country      = Countries::search_country($request->country);
            $user->save();

            $accessToken    = $user->createToken('CRIMSON-TOKEN')->accessToken;

            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Registered user successfully",
                "token" => $accessToken
            ],200);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "result" => false,
                "reason" => $th->getMessage()
            ],500);
        }
    }
}
