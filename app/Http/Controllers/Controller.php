<?php

namespace App\Http\Controllers;

use App\Services\Traits\ResponseCodeTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseCodeTrait;

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     */
    protected function validate(array $data, array $rules, array $messages = [])
    {
        if (!empty($messages)) {
            $validator = Validator::make($data, $rules, $messages);
        } else {
            $validator = Validator::make($data, $rules);
        }

        if ($validator->fails()) {
            $response = self::getResponseCode(101);
            $response['data'] = json_encode($validator->errors()->all());
            throw new HttpResponseException($this->response($response));
        }
    }

    /**
     * @param $return
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response($return)
    {
        $http_code = 200;
        if (isset($return['http_code'])) {
            $http_code = $return['http_code'];
            unset($return['http_code']);
        }

        return response()->json($return, $http_code);
    }
}
