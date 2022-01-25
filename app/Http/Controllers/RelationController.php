<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relation;
use App\Models\User;
use DB;
use App\Http\Requests\ValidRequest;
use App\Helpers\Badges;
use App\Helpers\Countries;

class RelationController extends Controller
{
    public function add_relation(ValidRequest $request){
        DB::beginTransaction();
        try {
            $user = User::where("email",$request->account)->first();
            $exist = Relation::where([
                ["id_user",Auth()->user()->id],
                ["id_relation",$user->id]
            ])->first();

            if( $exist ){
                return response()->json([
                    "result" => false,
                    "message" => "The relationship already exists",
                ],401);
            }

            $relation = new Relation;
            $relation->id_user = Auth()->user()->id;
            $relation->id_relation = $user->id;
            $relation->save();

            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Relationship added successfully",
            ],200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "result" => false,
                "reason" => $th->getMessage()
            ],500);
        }

    }

    public function delete_relation(ValidRequest $request){
        $validate = $this->validate(request(),[
            'account'   => 'required|email'
        ]);
        DB::beginTransaction();
        try {
            $user = User::where("email",$request->account)->first();
            Relation::where([
                ["id_user",Auth()->user()->id],
                ["id_relation",$user->id]
            ])->delete();
            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Relationship deleted successfully",
            ],200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "result" => false,
                "reason" => $th->getMessage()
            ],500);
        }
    }

    public function get_relations(Request $request){
        switch ($request->filter) {
            case 'direct':
                $relations = $this->get_relations_direct();
                break;
            case 'indirect':
                $relations = $this->get_relations_indirect();
                break;
            default:
                $relations = $this->get_relations_direct();
                break;
        }

        return response()->json([
            "result" => true,
            "message" => "Successful relationships",
            "relations" => $relations
        ],200);
    }

    public function get_relations_direct(){
        $relations = array();
        foreach (Auth()->user()->relations as $key => $relation) {
            array_push( $relations, array(
                "first_name"    => $relation->first_name,
                "last_name"     => $relation->last_name,
                "email"         => $relation->email,
                "country"       => Countries::search_iso( $relation->country ),
                "badge"         => Badges::badge( count($relation->relations) )
            ));
        }

        return $relations;
    }

    public function get_relations_indirect(){
        $relations = array();
        foreach (Auth()->user()->relations as $key => $relation) {
            if( count($relation->relations)>0 ){
                $relationships = array();
                foreach ($relation->relations as $key => $value) {
                    $array = array(
                        "first_name"    => $value->first_name,
                        "last_name"     => $value->last_name,
                        "email"         => $value->email,
                        "country"       => Countries::search_iso( $value->country ),
                        "badge"         => Badges::badge( count($value->relations) ),
                        "relations"     => array()
                    );
                    if( !in_array($array, $relations) ){
                        array_push($relationships,array(
                            "first_name"    => $value->first_name,
                            "last_name"     => $value->last_name,
                            "email"         => $value->email,
                            "country"       => Countries::search_iso( $value->country ),
                            "badge"         => Badges::badge( count($value->relations) )
                        ));
                    }
                }
            }else{
                $relationships = array();
            }
            array_push( $relations, array(
                "first_name"    => $relation->first_name,
                "last_name"     => $relation->last_name,
                "email"         => $relation->email,
                "country"       => Countries::search_iso( $relation->country ),
                "badge"         => Badges::badge( count($relation->relations) ),
                "relations"     => $relationships
            ));
        }

        return $relations;
    }

    public function random_relations(Request $request){
        $validate = $this->validate(request(),[
            'number_relations'   => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            $ids = array();
            array_push($ids,Auth()->user()->id);
            foreach (Auth()->user()->relations as $key => $relation) {
                array_push( $ids, $relation->id);
            }
            $users = User::whereNotIn('id',$ids)->inRandomOrder()->take($request->number_relations)->get();

            foreach ($users as $key2 => $user) {
                $relation = new Relation;
                $relation->id_user = Auth()->user()->id;
                $relation->id_relation = $user->id;
                $relation->save();
            }

            DB::commit();

            return response()->json([
                "result" => true,
                "message" => "Relationships added successfully",
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
