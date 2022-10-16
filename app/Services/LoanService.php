<?php

namespace App\Services;

use App\Repositories\LoanRepository;
use Carbon\Carbon;

class LoanService
{
    protected $loan_repository;
    protected $repayment_service;

    /**
     * LoanService constructor.
     * @param LoanRepository $loan_repository
     * @param \App\Services\RepaymentService $repayment_service
     */
    public function __construct(LoanRepository $loan_repository, RepaymentService $repayment_service)
    {
        $this->loan_repository = $loan_repository;
        $this->repayment_service = $repayment_service;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createLoan($data)
    {
        return $this->loan_repository->create($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updateLoan($data)
    {
        $params = ['status' => $data['status']];
        $where_clause = ['id' => $data['loan_id']];
        if ($data['status'] == 'APPROVED') {
            $params['loan_approved_date'] = Carbon::now();
        }

        $loan_data = $this->loan_repository->update($params, $where_clause);

        if ($loan_data->status == 'APPROVED') {
            $this->repayment_service->calculateRepayment($loan_data);
        }

        return $loan_data;
    }

    public function checkUserActiveLoan($user_id)
    {
        return $this->loan_repository->getUserActiveLoan($user_id);
    }
}
