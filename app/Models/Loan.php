<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'term',
        'status',
        'payment_status',
    ];
}
