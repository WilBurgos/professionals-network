<?php

namespace App\Helpers;

class Badges {

    public function levels(){
        return array(
            [
                "level"         => "Normal",
                "min_relations" => 0,
                "max_relations" => 10
            ],
            [
                "level"         => "Bronce",
                "min_relations" => 11,
                "max_relations" => 50
            ],
            [
                "level"         => "Silver",
                "min_relations" => 51,
                "max_relations" => 99
            ],
            [
                "level"         => "Gold",
                "min_relations" => 100,
                "max_relations" => 144
            ],
            [
                "level"         => "Platinum",
                "min_relations" => 145,
                "max_relations" => null
            ]
        );
    }

    public function badge($number){
        foreach (self::levels() as $key => $level) {
            if( $number >= $level["min_relations"] && $number <= $level["max_relations"] ){
                return $level["level"];
            }
        }
    }

}
