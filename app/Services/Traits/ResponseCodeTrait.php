<?php

namespace App\Services\Traits;

trait ResponseCodeTrait
{
    /**
     * to get data for responseCode
     * @param int $code Response code param
     * @return array
     */
    public function getResponseCode($code)
    {
        $responseCode = [
            /*
            |--------------------------------------------------------------------------
            | GENERAL SUCCESS RESPONSE CODE
            |--------------------------------------------------------------------------
            */
            '1' => ['success' => true, 'response_code' => 0, 'message' => 'Success', 'http_code' => 200],

            /*
            |--------------------------------------------------------------------------
            | GENERAL ERROR RESPONSE CODE
            |--------------------------------------------------------------------------
            */
            '101' => ['success' => false, 'response_code' => 101, 'message' => 'Validation errors', 'http_code' => 400],
            '102' => ['success' => false, 'response_code' => 102, 'message' => 'Application errors', 'http_code' => 200],
            '103' => ['success' => false, 'response_code' => 104, 'message' => 'Record not found', 'http_code' => 404],

        ];

        return $responseCode[$code];
    }
}
