<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\RepaymentService;
use Illuminate\Http\Request;

class RepaymentController extends Controller
{
    protected $repayment_service;

    /**
     * RepaymentController constructor.
     * @param RepaymentService $repayment_service
     */
    public function __construct(RepaymentService $repayment_service)
    {
        $this->repayment_service = $repayment_service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function repay(Request $request)
    {
        $request_data = $request->all();

        $rules = [
            'loan_id' => 'required',
            'amount' => 'required'
        ];
        $this->validate($request_data, $rules);

        $response_data = $this->repayment_service->repay($request_data);

        $response = self::getResponseCode(1);
        if ($response_data) {
            $response['message'] = 'Repayment done successfully.';
        } else {
            $response['message'] = 'Loan is already paid.';
        }

        return $this->response($response);
    }
}
