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
     * add new loan
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

                // Create loan repayments
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
     * get customer's all loans
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

            // Check if the loan repayment exists
            if (!$loanRepayment) {
                return $this->sendError("Loan repayment doesn't exist.", []);
            }
            // Check if the loan belongs to the current user
            if ($loanRepayment->getLaon->user_id !== Auth::id()) {
                return $this->sendError("Wrong loan repayment user.", []);
            }
            // Check if the loan is approved
            if ($loanRepayment->getLaon->status !== 'APPROVED') {
                return $this->sendError('Loan is not approved.', []);
            }
            // Check if the loan repayment already paid
            if ($loanRepayment->payment_status !== 'NOT_PAID') {
                return $this->sendError('Loan repayment is already paid.', []);
            }

            // Define all data variables
            $customer_amount = sprintf("%.3f", $request->input('amount'));
            $repayment_amount = $loanRepayment->amount;
            $loan_id = $loanRepayment->loan_id;
            $loan = Loan::find($loan_id);
            // Success response Loan Repayment Resource
            $response['loan_repayment'] = new LoanRepaymentResource($loanRepayment);

            // Loan repayment with different amount coditions
            if ($customer_amount == $repayment_amount) {
                // If loan repayment amount is equal to scheduled repayment amount, Update repayment status to PAID
                $loanRepayment->payment_status = 'PAID';
                $loanRepayment->save();
            } elseif ($customer_amount > $repayment_amount) {
                // Update repayment status to PAID
                $loanRepayment->payment_status = 'PAID';
                $loanRepayment->save();

                // If loan repayment amount is greater than scheduled repayment amount
                $is_extra_amount = true;
                $extra_amount = $customer_amount - $repayment_amount;

                while ($is_extra_amount) {
                    // Get customer last not paid scheduled repayment
                    $lastLoanRepayment = LoanRepayment::where([
                        'loan_id' => $loan_id,
                        'payment_status' => 'NOT_PAID'
                    ])->orderByDesc('id')->first();

                    if ($lastLoanRepayment) {
                        if ($lastLoanRepayment->amount == $extra_amount) {
                            $lastLoanRepayment->payment_status = 'PAID';
                            $lastLoanRepayment->save();
                            $is_extra_amount = false;
                        } elseif ($lastLoanRepayment->amount > $extra_amount) {

                            // calculate remaining last repayment amount
                            $remain_repayment_amount = $lastLoanRepayment->amount - $extra_amount;

                            // checking is extra payment done before
                            if ($lastLoanRepayment->remarks) {
                                $remarks = explode('$', $lastLoanRepayment->remarks)[0];
                                $paid_amount = sprintf("%.3f", $remarks) + $extra_amount;
                            }

                            // saving amount
                            $lastLoanRepayment->amount = $remain_repayment_amount;
                            $lastLoanRepayment->remarks = ($lastLoanRepayment->remarks ? $paid_amount : $extra_amount) . "$ is PAID. Date " . date('Y-m-d');
                            $lastLoanRepayment->save();
                            $is_extra_amount = false;
                        } else {
                            $lastLoanRepayment->payment_status = 'PAID';
                            $lastLoanRepayment->save();
                            $extra_amount = $extra_amount - $lastLoanRepayment->amount;
                        }
                    } else {
                        // If All loan repayments are PAID, then Loan payment_status update to PAID
                        if (!$loan->getRemainLoanRepayment->count()) {
                            $loan->payment_status = "PAID";
                            $loan->save();
                        }
                        if ($extra_amount)
                            return $this->sendResponse($response, "Customer loan repayment successfull. Customer doesn't have any other repayments.{$extra_amount}$ is remaining extra amount.");
                    }
                }
            } else {
                // If loan repayment amount is less than scheduled repayment amount
                return $this->sendError('Loan repayment amount must greater or equal to the scheduled repayment.', []);
            }

            // If All loan repayments are PAID, then Loan payment_status update to PAID
            if (!$loan->getRemainLoanRepayment->count()) {
                $loan->payment_status = "PAID";
                $loan->save();
            }
            return $this->sendResponse($response, "Customer loan repayment successfull");
        }
    }
}
