<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Repositories\Interfaces\RepositoryInterface;

class LoanRepository implements RepositoryInterface
{
    protected $model;

    /**
     * LoanRepository constructor.
     * @param Loan $loan
     */
    public function __construct(Loan $loan)
    {
        $this->model = $loan;
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
     * @param $id
     * @return mixed
     */
    public function getUserActiveLoan($user_id)
    {
        return $this->model->where('user_id', $user_id)->where('status', 'APPROVED')->first();
    }
}
