<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_register()
    {
        $response = $this->post('/api/v1/user/register', [
            "name" => "feature test",
            "mobile" => "9999988888",
            "email" => "feature@test.com",
            "password" => 12345678]);

        $response->assertStatus(200);
    }

    public function test_apply_loan()
    {
        $login_response = $this->post('/api/v1/user/login', [
            'mobile' => '9999988888',
            'password' => 12345678
        ]);

        $login_response->assertStatus(200);
        $token = $login_response['data']['token'];


        $response = $this->post('/api/v1/user/apply-loan', [
            'amount' => 10,
            'term' => 3
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    public function test_approve_loan()
    {
        User::factory()->count(1)->create(['password' => bcrypt(12345678), 'mobile' => '9999999911', 'is_admin' => 1]);
        $login_response = $this->post('/api/v1/user/login', [
            'mobile' => '9999999911',
            'password' => 12345678
        ]);

        $login_response->assertStatus(200);
        $token = $login_response['data']['token'];

        $response = $this->post('/api/v1/admin/update-loan-status', [
            'loan_id' => 1,
            'status' => "APPROVED"
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    public function test_pay_one_full_repayment()
    {
        $login_response = $this->post('/api/v1/user/login', [
            'mobile' => '9999988888',
            'password' => 12345678
        ]);

        $login_response->assertStatus(200);
        $token = $login_response['data']['token'];


        $response = $this->post('/api/v1/user/repay', [
            'loan_id' => 1,
            'amount' => 3.33
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    public function test_pay_one_partial_repayment()
    {
        $login_response = $this->post('/api/v1/user/login', [
            'mobile' => '9999988888',
            'password' => 12345678
        ]);

        $login_response->assertStatus(200);
        $token = $login_response['data']['token'];


        $response = $this->post('/api/v1/user/repay', [
            'loan_id' => 1,
            'amount' => 5
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    public function test_pay_all_repayment()
    {
        $login_response = $this->post('/api/v1/user/login', [
            'mobile' => '9999988888',
            'password' => 12345678
        ]);

        $login_response->assertStatus(200);
        $token = $login_response['data']['token'];


        $response = $this->post('/api/v1/user/repay', [
            'loan_id' => 5,
            'amount' => 10
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }
}
