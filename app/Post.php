<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // creating relation with the users table
    public function user(){
        return $this->belongsTo('App\User');
    }

}
