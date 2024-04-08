<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{

    protected $connection = 'mongodb';
    protected $fillable = ['id', 'value', 'values', 'min', 'max', 'location', 'port'];

}
