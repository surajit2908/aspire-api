<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\LoanRepayment;

class LoanRepaymentResource extends JsonResource
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
            'amount' => $this->amount,
            'repayment_date' => $this->repayment_date,
            'remarks' => $this->remarks,
            'payment_status' => $this->payment_status,
        ];
    }
}
