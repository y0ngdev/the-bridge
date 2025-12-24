<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenure extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['name', 'year'];

    public function alumni(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Alumnus::class);
    }
}
