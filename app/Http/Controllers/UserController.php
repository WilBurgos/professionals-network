<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Countries;
use DB;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\ValidRequest;

class UserController extends Controller
{
    public function update_user(UserRequest $request){
        DB::beginTransaction();
        try {
            $upd_user               = User::find(Auth()->user()->id);
            $upd_user->first_name   = $request->first_name ? $request->first_name : $upd_user->first_name;
            $upd_user->last_name    = $request->last_name ? $request->last_name : $upd_user->last_name;
            $upd_user->email        = $request->email ? $request->email : $upd_user->email;
            $upd_user->password     = $request->password ? Crypt::encrypt($request->password) : $upd_user->password;
            $upd_user->country      = $request->country ? Countries::search_country($request->country) : $upd_user->country;
            $upd_user->save();

            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "User updated successfully",
            ],200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "result" => false,
                "reason" => $th->getMessage()
            ],500);
        }
    }

    public function delete_user(ValidRequest $request){
        $delete = User::find(Auth()->user()->id);
        $delete->delete();

        return response()->json([
            "result" => true,
            "message" => "User eliminated successfully",
        ],200);
    }

    public function import_users(Request $request){
        $validate = $this->validate(request(),[
            'number_of_users'   => 'required|numeric'
        ]);
        DB::beginTransaction();
        try {
            User::factory()->count($request->number_of_users)->create();
            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Successfully imported users",
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
