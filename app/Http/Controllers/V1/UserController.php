<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Traits\ResponseCodeTrait;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JWTAuth;

class UserController extends Controller
{
    use ResponseCodeTrait;

    protected $user_service;

    /**
     * UserController constructor.
     * @param UserService $user_service
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request_data = $request->all();

        $rules = [
            'name' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ];
        $this->validate($request_data, $rules);

        $response_data = $this->user_service->createUser($request_data);

        $response = self::getResponseCode(1);
        $response['data']['user'] = new UserResource($response_data);

        return $this->response($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request_data = $request->only(['mobile', 'password']);

        $rules = [
            'mobile' => 'required',
            'password' => 'required'
        ];
        $this->validate($request_data, $rules);

        $response_data = $this->user_service->userLogin($request_data);
        $response = self::getResponseCode(1);
        if (!empty($response_data)) {
            $response['data']['token'] = $response_data;
            $response['data']['user'] = new UserResource(\Auth::user());
        } else {
            $response['message'] = 'Invalid Credentials';
        }

        return $this->response($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $response = self::getResponseCode(1);
        $response['data']['user'] = new UserResource($user);

        return $this->response($response);
    }
}
