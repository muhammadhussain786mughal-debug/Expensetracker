<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expense extends Model
{
    protected $table='expenses';
    protected $fillable = [
        'reciept_id',
        'Mart_name',
        'Date_Expense',
        'total_amount',

    ];
}
