<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
    //

    public function category(){
        return $this->belongsTo('App\Models\Catagories', 'cat_id');
    }
       
}