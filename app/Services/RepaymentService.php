<?php

namespace App\Services;

use App\Repositories\LoanRepository;
use App\Repositories\RepaymentRepository;
use App\Services\Traits\ResponseCodeTrait;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class RepaymentService
{
    use ResponseCodeTrait;

    protected $repayment_repository;
    protected $loan_repository;

    /**
     * RepaymentService constructor.
     * @param RepaymentRepository $repayment_repository
     */
    public function __construct(RepaymentRepository $repayment_repository, LoanRepository $loan_repository)
    {
        $this->repayment_repository = $repayment_repository;
        $this->loan_repository = $loan_repository;
    }

    /**
     * @param $loan_data
     * @return array
     */
    public function calculateRepayment($loan_data)
    {
        $repayment_data = [];
        $repayment_amount = 0;
        $pending_repayment_amount = $loan_data->amount;
        $repayment_date = Carbon::today();
        for ($i = 0; $i < $loan_data->term; $i++) {
            //Loan Repayment calculate logic
            if ($i == 0) {
                $repayment_amount = $loan_data->amount / $loan_data->term;
            }
            if ($i == ($loan_data->term - 1)) {
                $repayment_amount = $pending_repayment_amount;
            } else {
                $pending_repayment_amount = round($pending_repayment_amount, 2) - round($repayment_amount, 2);
            }

            //weekly loan repayment data
            $repayment_data[$i] = [
                'loan_id' => $loan_data->id,
                'payment_amount' => round($repayment_amount, 2),
                'scheduled_paymeny_date' => $repayment_date->addDays(7)->format('Y-m-d')
            ];
        }
        //create weekly loan repayment
        $this->repayment_repository->createMany($repayment_data);

        return $repayment_data;
    }

    /**
     * @param $data
     * @return bool
     */
    public function repay($data)
    {
        $result = false;
        $repayment_data = $this->repayment_repository->getUnpaidLoanRepayments($data['loan_id']);

        if ($repayment_data->isNotEmpty()) {
            $first_repayment_amount = ($repayment_data->first()->status == 'PENDING') ? $repayment_data->first()->payment_amount : $repayment_data->first()->pending_amount;
            $total_repaymeny_amount = $repayment_data->where('status', 'PARTIAL_PAID')->sum('pending_amount') + $repayment_data->where('status', 'PENDING')->sum('payment_amount');

            if ($data['amount'] < $first_repayment_amount) {
                $response = self::getResponseCode(101);
                $response['message'] = 'Amount should be greater or equal to repayment amount.';
                throw new HttpResponseException(response()->json($response, $response['http_code']));
            }

            if ($data['amount'] > $total_repaymeny_amount) {
                $response = self::getResponseCode(101);
                $response['message'] = 'Amount should be less then or equal to total repayment amount.';
                throw new HttpResponseException(response()->json($response, $response['http_code']));
            }

            if ($data['amount'] == $first_repayment_amount) {
                $param = ['status' => 'PAID'];
                $where_clause = ['id' => $repayment_data->first()->id];
                $this->repayment_repository->update($param, $where_clause);
            } elseif ($data['amount'] > $first_repayment_amount && $data['amount'] < $total_repaymeny_amount) {
                $i = 0;
                $amount = $data['amount'];
                do {
                    $payment_amount = ($repayment_data[$i]['status'] == 'PENDING') ? $repayment_data[$i]['payment_amount'] : $repayment_data[$i]['pending_amount'];
                    if ($amount >= $payment_amount) {
                        $amount = $amount - $payment_amount;
                        $param = ['status' => 'PAID', 'pending_amount' => 0];
                    } else {
                        $pending_amount = $payment_amount - $amount;
                        $amount = 0;
                        $param = ['status' => 'PARTIAL_PAID', 'pending_amount' => $pending_amount];
                    }

                    $where_clause = ['id' => $repayment_data[$i]['id']];
                    $this->repayment_repository->update($param, $where_clause);
                    $i++;
                } while ($amount > 0);
            } else {
                $param = ['status' => 'PAID'];
                $where_clause = ['loan_id' => $repayment_data->first()->loan_id];
                $this->repayment_repository->updateMany($param, $where_clause);
            }

            //If all repayment paid update loan status to CLOSED
           if (count($this->repayment_repository->getUnpaidLoanRepayments($data['loan_id'])) == 0) {
               $param = ['status' => 'CLOSED'];
               $where_clause = ['id' => $data['loan_id']];
               $this->loan_repository->update($param, $where_clause);
           }

            $result = true;
        }

        return $result;
    }
}
