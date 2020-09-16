<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $fillable = ['jsonData'];
    protected $hidden = ['created_at', 'updated_at'];

    //
}
