<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{

    protected $fillable = [
        'id',
        'loan_id',
        'amount',
        'repayment_date',
        'payment_status',
    ];

    public function laon()
    {
        return $this->belongsTo(Loan::class, "id", "loan_id");
    }
}
