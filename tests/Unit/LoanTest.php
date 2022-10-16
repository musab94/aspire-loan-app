<?php

namespace Tests\Unit;

use App\Models\LoanRepayment;
use App\Services\RepaymentService;
use App\Repositories\RepaymentRepository;
use App\Repositories\LoanRepository;
use App\Models\Loan;
use Carbon\Carbon;
use Tests\TestCase;

class LoanTest extends TestCase
{
    protected $repayment_repository;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_calculate_laon_repayment()
    {
        $loan_data = [
            'id' => 1,
            'amount' => 10,
            'term' => 3
        ];
        $loan_data = (object) $loan_data;
        $today_date = Carbon::today();
        $loan = new Loan();
        $repayment = new LoanRepayment();
        $loan_repo = new LoanRepository($loan);
        $repayment_repo = new RepaymentRepository($repayment);

        $response = (new RepaymentService($repayment_repo, $loan_repo))->calculateRepayment($loan_data);
        $expected_result = [
            [
                "loan_id" => 1,
                "payment_amount" => 3.33,
                "scheduled_paymeny_date" => $today_date->addDays(7)->format('Y-m-d')
            ],
            [
                "loan_id" => 1,
                "payment_amount" => 3.33,
                "scheduled_paymeny_date" => $today_date->addDays(7)->format('Y-m-d')
            ],
            [
                "loan_id" => 1,
                "payment_amount" => 3.34,
                "scheduled_paymeny_date" => $today_date->addDays(7)->format('Y-m-d')
            ]
        ];
        $this->assertEquals($expected_result, $response);
    }
}
