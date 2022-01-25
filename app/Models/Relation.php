<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $table = 'relationship';

    public function user()
    {
      return $this->belongsTo('App\Models\User','id_user');
    }

    public function relations()
    {
      return $this->belongsTo('App\Models\User','id_relation');
    }

}
