<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Services\LoanService;
use App\Services\Traits\ResponseCodeTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanController extends Controller
{
    use ResponseCodeTrait;

    protected $loan_service;

    /**
     * LoanController constructor.
     * @param LoanService $loan_service
     */
    public function __construct(LoanService $loan_service)
    {
        $this->loan_service = $loan_service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyLoan(Request $request)
    {
        $request_data = $request->all();
        $request_data['user_id'] = $request->user()->id;

        $rules = [
            'amount' => 'required',
            'term' => 'required',
            'user_id' => 'required'
        ];
        $this->validate($request_data, $rules);

        $response = self::getResponseCode(1);
        $check_active_loan = $this->loan_service->checkUserActiveLoan($request_data['user_id']);
        if (!$check_active_loan) {
            $response_data = $this->loan_service->createLoan($request_data);
            $response['data']['loan'] = new LoanResource($response_data);
        } else {
            $response['message'] = 'Please pay the active loan first.';
        }

        return $this->response($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLoanStatus(Request $request)
    {
        $response = self::getResponseCode(1);
        if ($request->user()->is_admin) {
            $request_data = $request->all();
            $rules = [
                'loan_id' => 'required',
                'status' => 'required|in:APPROVED,REJECTED',
            ];
            $this->validate($request_data, $rules);

            $response_data = $this->loan_service->updateLoan($request_data);
            $response['data']['loan'] = new LoanResource($response_data);
        } else {
            $response['message'] = 'Unauthorized user';
        }

        return $this->response($response);
    }
}
