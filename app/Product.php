<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model 
{
    protected $guarded = [];

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}