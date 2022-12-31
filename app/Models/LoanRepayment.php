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
        'remarks',
        'payment_status',
    ];

    public function getLaon()
    {
        return $this->belongsTo(Loan::class, "loan_id", "id");
    }
}
