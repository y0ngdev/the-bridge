<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tenure extends Model
{

    protected $fillable = ['name', 'year'];

//    public function alumni()
//    {
//        return $this->hasMany(Alumnus::class);
//    }
}
