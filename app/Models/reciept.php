<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reciept extends Model
{
    protected $table='reciepts';
    protected $fillable = [
        'image',
    ];
}
