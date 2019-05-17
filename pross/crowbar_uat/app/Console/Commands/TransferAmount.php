<?php

    namespace App\Console\Commands;
    use Illuminate\Console\Command;

    class TransferAmount extends Command{
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'transferamount';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Transfer payment to talent paypal account after the project closure.';

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
            $current_date   = '2018-07-22';
            
            $prefix         = \DB::getTablePrefix();
            
            $projects = \Models\Projects::addSelect(['id_project','title','enddate','user_id as company_id','created as created_at'])
            ->projectClosureStatus()
            ->with([
                'proposal' => function($q){
                    $q->addSelect(['id_proposal','project_id','user_id'])->where('talent_proposals.status','accepted')->with([
                        'talent' => function($q){
                            $q->addSelect(['id_user','paypal_id','paypal_payer_id']);
                        }
                    ]);
                }
            ])
            ->withCount([
                'dispute',
                'projectlog',
                'proposal' => function($q){
                    $q->where('talent_proposals.status','accepted');
                },
                'transaction' => function($q){
                    $q->where('transactions.transaction_type','credit');
                }
            ])
            ->whereNotNull('projects.enddate')
            ->whereNotNull('projects.closedate')
            ->where('projects.startdate', '>=', $current_date)
            // ->having('projectlog_count','>',0) //prob
            ->having('proposal_count','>',0)
            ->having('dispute_count','<',1) //prob
            ->having('transaction_count','<',1)
            ->having('project_closure_status','=','closed')
            ->orderBy('id_project','DESC')
            ->get();

            
            #dd(json_decode(json_encode($projects)));
            
            if(!empty($projects->count())){

                $cronRecord = [];
                $cronRecord = [
                    'name'       => 'transferamount cron',
                    'status'     => 'active',
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $responce = \DB::table('cron_log')->insertGetId($cronRecord);

                foreach ($projects as $key) {
                    if(!empty($key->id_project)){
                        $isPayOutDone       = \Models\Payments::init_talent_payment($key->id_project,-1);
                        $isProposalUpdated  = \Models\Proposals::where('project_id',$key->id_project)->where('status','accepted')->update(['payment' => 'confirmed','updated' => date('Y-m-d H:i:s')]);
                        
                        $isNotified = \Models\Notifications::notify(
                            $key->proposal->talent->id_user,
                            $key->company_id,
                            'JOB_PAYMENT_RELEASED_BY_CROWBAR',
                            json_encode([
                                "receiver_id" => (string) $key->proposal->talent->id_user,
                                "sender_id" => (string) $key->company_id,
                                "project_id" => (string) $key->id_project
                            ])
                        );
                    }
                }

            }

        }
    }
