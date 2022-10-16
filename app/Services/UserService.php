<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $user_repository;

    /**
     * UserService constructor.
     * @param UserRepository $user_repository
     */
    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        $params['name'] = $data['name'];
        $params['email'] = $data['email'];
        $params['mobile'] = $data['mobile'];
        $params['password'] = bcrypt($data['password']);

        if (!empty($data['is_admin']) && $data['is_admin']) {
            $params['is_admin'] = $data['is_admin'];
        }

        return $this->user_repository->create($params);
    }

    /**
     * @param $data
     * @return bool
     */
    public function userLogin($data)
    {
        $token = Auth::attempt($data);

        return $token;
    }
}
