<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\LoanRepayment;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user_id ? User::find($this->user_id)->name : null,
            'amount' => $this->amount,
            'term' => $this->term,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'loan_date' => date('Y-m-d', strtotime($this->created_at)),
            'loan_repayment' => LoanRepaymentResource::collection(LoanRepayment::whereLoanId($this->id)->get()),
        ];
    }
}
