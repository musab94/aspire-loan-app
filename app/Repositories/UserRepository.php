<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\RepositoryInterface;

class UserRepository implements RepositoryInterface
{
    protected $model;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
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
     * @param $mobile_no
     * @return mixed
     */
    public function findByMobile($mobile_no)
    {
        return $this->model->where('mobile', $mobile_no)->first();
    }
}
