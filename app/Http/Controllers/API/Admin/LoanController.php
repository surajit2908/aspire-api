<?php

namespace App\Http\Controllers\API\Admin;

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
     * get all loan
     */
    public function list()
    {
        $loans = Loan::get();
        $response['loans'] = LoanResource::collection($loans);
        return $this->sendResponse($response, 'Loans are retrieved successfully');
    }

    /**
     * approve loan by admin
     */
    public function approveLoan($loan_id)
    {
        $loan  = Loan::find($loan_id);
        $loan->status = 'APPROVED';
        $loan->save();

        $response['loan'] = new LoanResource($loan);
        return $this->sendResponse($response, 'Loan approved successfull');
    }
}
