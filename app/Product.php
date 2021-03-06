<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    use HasFactory;

    public $gain;

    protected $guarded = [];

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
