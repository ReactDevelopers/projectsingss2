<?php

	namespace App\Console\Commands;
    use Illuminate\Console\Command;

    class RefundPayment extends Command{
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'refundpayment';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Refund payment to employer.';

        /**
         * Create a new command instance.
         *
         * @return void
         */
        public function __construct(){
            parent::__construct();
        }

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle(){   
            $refund_transaction = \Models\Transactions::where(['transaction_status' => 'refunded-pending'])
            ->whereRaw('DATE(transaction_date) >= "'.date('Y-m-d').'"')
            ->whereNull('transaction_reference_id')->limit(10)->get();
            
            if(!empty($refund_transaction->count())){
                foreach ($refund_transaction as $key) {
                    $result = \Models\PaypalPayment::refund([
                        'id_transactions'   => $key['id_transactions'],
                        'user_id'           => $key['transaction_user_id'],
                        'sale_id'           => $key['transaction_comment'], 
                        'refund_amount'     => $key['transaction_total']
                    ]);
                }
            }
        }
    }
