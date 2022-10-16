<?php

namespace App\Repositories;

use App\Models\LoanRepayment;
use App\Repositories\Interfaces\RepositoryInterface;

class RepaymentRepository implements RepositoryInterface
{
    protected $model;

    /**
     * RepaymentRepository constructor.
     * @param LoanRepayment $repayment
     */
    public function __construct(LoanRepayment $repayment)
    {
        $this->model = $repayment;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function create($params)
    {
        return $this->model->create($params);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function createMany($params)
    {
        return $this->model->insert($params);
    }

    /**
     * @param $params
     * @param $where_clause
     * @return mixed
     */
    public function update($params, $where_clause)
    {
        $entity = $this->model->where($where_clause)->first();
        if (!empty($entity)) {
            $entity->update($params);
        }

        return $entity;
    }

    /**
     * @param $params
     * @param $where_clause
     * @return mixed
     */
    public function updateMany($params, $where_clause)
    {
        return $this->model->where($where_clause)->update($params);
    }

    /**
     * @param $loan_id
     * @return mixed
     */
    public function getUnpaidLoanRepayments($loan_id)
    {
        return $this->model->where('loan_id', $loan_id)->where('status', '<>', 'PAID')->get();
    }
}
