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

    // get not paid loan repayment
    public function getRemainLoanRepayment()
    {
        return $this->hasMany(LoanRepayment::class, "loan_id", "id")->wherePaymentStatus('NOT_PAID');
    }
}
