<?php

    namespace App\Console\Commands;
    use Illuminate\Console\Command;

    class RaiseDisputeStatusUpdate extends Command{
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'raisedisputestatusupdate';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Update status if timer expired on both end.';

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
            $current_date = date('Y-m-d H:i:s');
            $prefix = \DB::getTablePrefix();

            $raise_dispute_types            = \Models\Listings::raise_dispute_type_column();


            $disputes = \Models\RaiseDispute::whereRaw("
                ('{$current_date}' > DATE_ADD({$prefix}projects_dispute.last_updated, INTERVAL ".RAISE_DISPUTE_STEP_1_HOURS_LIMIT." HOUR))
            ")->where('type','!=','receiver-final-comment')->get();
            
            foreach($disputes as $item){
                $raise_dispute_index    = array_search($item->type, $raise_dispute_types);
                $next_type              = $raise_dispute_types[($raise_dispute_index+1)];

                $isUpdated = \Models\RaiseDispute::where('id_raised_dispute',$item->id_raised_dispute)
                ->update([
                    'type'              => $next_type,
                    'last_commented_by' => SUPPORT_CHAT_USER_ID,
                    'last_updated'      => date('Y-m-d H:i:s'),
                    'updated'           => date('Y-m-d H:i:s')
                ]);

                if(in_array($next_type, ['sender-comment','sender-final-comment'])){
                    $comment            = trans('website.employer_not_responded');
                }else{
                    $comment            = trans('website.talent_not_responded');
                }

                $commentArray = [
                    'dispute_id'        => $item->id_raised_dispute,
                    'sender_id'         => SUPPORT_CHAT_USER_ID,
                    'comment'           => $comment,
                    'type'              => $next_type,
                    'updated'           => date('Y-m-d H:i:s'),
                    'created'           => date('Y-m-d H:i:s'),
                ];

                $isCommentCreated = \Models\RaiseDisputeComments::submit($commentArray);
            }
        }
    }
