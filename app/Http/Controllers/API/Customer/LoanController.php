<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Http\Resources\LoanResource;
use App\Http\Resources\LoanRepaymentResource;
use Carbon\Carbon;

/**
 * Class LoanController
 */
class LoanController extends AppBaseController
{

    /**
     * add new loans
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'term' => 'required|integer'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        } else {
            try {
                DB::beginTransaction();

                // Insert Into DB, loans Table
                $loan = new Loan();
                $loan->user_id = Auth::id();
                $loan->amount = $request->input('amount');
                $loan->term = $request->input('term');
                $loan->save();

                // get repayment ammount
                $repayment_amount = sprintf("%.3f", $request->input('amount') / $request->input('term'));
                $repayment_sum = 0.000;

                for ($i = 1; $i <= $request->input('term'); $i++) {
                    $days = $i * 7;
                    $repaymentDateTime = Carbon::now()->addDays($days);

                    // calculate last remaining repayment ammount
                    if ($i == $request->input('term'))
                        $repayment_amount = $request->input('amount') - $repayment_sum;

                    // sum all repayments to calculate last remaining repayment ammount
                    $repayment_sum += $repayment_amount;

                    // Insert Into DB, loan_repayments Table
                    $loanRepayment = new LoanRepayment();
                    $loanRepayment->loan_id = $loan->id;
                    $loanRepayment->amount = $repayment_amount;
                    $loanRepayment->repayment_date = $repaymentDateTime;
                    $loanRepayment->save();
                }

                DB::commit();

                $response = [];
                return $this->sendResponse($response, 'Loan request submitted successfully');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Loan Request Error.', $e->getMessage(), 500);
            }
        }
    }

    /**
     * get customer's all loan
     */
    public function list()
    {
        $loans = Loan::whereUserId(Auth::id())->get();
        $response['loans'] = LoanResource::collection($loans);
        return $this->sendResponse($response, 'Customer loans are retrieved successfully');
    }

    /**
     * customer loan repayment add
     */
    public function addRepayment(Request $request, $loan_repayment_id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        } else {
            $loanRepayment = LoanRepayment::find($loan_repayment_id);
            if ($loanRepayment->loan->status === 'APPROVED') {

                if ($loanRepayment->payment_status === 'NOT_PAID') {
                    if ($loanRepayment->amount === $request->input('amount')) {
                        $loanRepayment->payment_status = 'PAID';
                        $loanRepayment->save();
                    } elseif ($loanRepayment->amount === $request->input('amount')) {
                    } else {
                    }
                } else {
                    return $this->sendError('Loan repayment is already paid.', []);
                }
            } else {
                return $this->sendError('Loan is not approved.', []);
            }

            $response['loan'] = new LoanRepaymentResource($loanRepayment);
            return $this->sendResponse($response, 'Customer loan repayment successfull');
        }
    }
}
