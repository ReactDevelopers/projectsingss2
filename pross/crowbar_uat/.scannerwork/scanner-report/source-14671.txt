<?php
    namespace App\Http\Controllers\Front;

    use App\Http\Requests;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\DB;

    use App\Http\Controllers\Controller;

    class DisputeController extends Controller{
    
	    /**
	     * Create a new controller instance.
	     *
	     * @return void
	     */
	    protected $jwt;
	    private $post;
	    private $token;
	    private $status;
        private $redirect;
        private $jsondata;
        private $status_code;
        private $prefix;

        public function __construct(Request $request){
            $this->jsondata     = (object)[];
            $this->message      = "M0000";
            $this->redirect         = false;
	        $this->error_code   = "no_error_found";
	        $this->status       = false;
	        $this->status_code  = 200;
	        $this->prefix       = \DB::getTablePrefix();

            \View::share ( 'footer_settings', \Cache::get('configuration') );

	        $json = json_decode(file_get_contents('php://input'),true);
	        if(!empty($json)){
	            $this->post = $json;
	        }else{
	            $this->post = $request->all();
	        }

	        /*RECORDING API REQUEST IN TABLE*/
	        \Models\Listings::record_api_request([
	            'url' => $request->url(),
	            'request' => json_encode($this->post),
	            'type' => 'webservice',
	            'created' => date('Y-m-d H:i:s')
	        ],$request);
	    }

	    private function populateresponse($data){
	        $data['message'] = (!empty($data['message']))?"":$this->message;
	        
	        if(empty($this->error)){
	            $data['error'] = trans(sprintf("general.%s",$data['message']));     
	        }else{
	            $data['error'] = $this->error;
	        }

	        $data['error_code'] = "";

	        if(empty($data['status'])){
	            $data['status'] = $this->status;
	            $data['error_code'] = $this->message;
	        }
	        
	        $data['status_code'] = $this->status_code;
	        
	        $data = json_decode(json_encode($data),true);

	        array_walk_recursive($data, function(&$item){
	            if (gettype($item) == 'integer' || gettype($item) == 'float' || gettype($item) == 'NULL'){
	                $item = trim($item);
	            }
	        });

	        if(empty($data['data'])){
	            $data['data'] = (object) $data['data'];
	        }

	        return $data;
	    }

        /**
         * [This method is used for submit raise dispute ]
         * @param  Request
         * @return Json\Response
         */

	    public function submit(Request $request){
            $project_id         = $request->project_id;
            $sender_id          = auth()->user()->id_user;
            $sender_type        = auth()->user()->type;

            $dispute_detail     = \Models\RaiseDispute::detail($project_id,$sender_id);
            
            $validator = \Validator::make($request->all(),[
                'project_id'                => ['required'],
                'comment'                   => validation('rasie_dispute_reason')
            ],[
                'reason.required'           => trans('general.M0539'),
                'comment.required'          => trans('general.M0384'),
                'comment.string'            => trans('general.M0385'),
                'comment.regex'             => trans('general.M0385'),
                'comment.max'               => trans('general.M0386'),
                'comment.min'               => trans('general.M0387'),
                'project_id.required'       => trans('general.M0121'),
            ]);

            $validator->sometimes(['reason'], validation('rasie_dispute_reason_id'), function($input) use($dispute_detail){
                return (!empty($dispute_detail) && $dispute_detail->next_type == 'sender-comment')?true:false;
            });

            $validator->after(function($validator) use($dispute_detail,$sender_id){
                if(empty($validator->errors()->first()) && (strtotime($dispute_detail->duration) >= strtotime(date('Y-m-d')) && $dispute_detail->last_commented_by == $sender_id)){
                    $validator->errors()->add('comment',trans('website.W0725'));
                }
            });

            if($validator->passes()){
                $project        = \Models\Projects::defaultKeys()
                ->with([
                    'proposal' => function($q){
                        $q->defaultKeys()->where('talent_proposals.status','accepted')->with([
                            'talent' => function($q){
                                $q->defaultKeys();
                            }
                        ]);
                    }
                ])
                ->where('id_project',$project_id)
                ->get()
                ->first();

                if(1/*$project->project_status != 'closed' && empty($dispute_detail)*/){
                    $next_type = $dispute_detail->next_type;
                    if(empty($dispute_detail->id_raised_dispute)){
                        $raiseArray = [
                            'project_id'        => $project_id,
                            'disputed_by'       => $sender_id,
                            'disputed_by_type'  => $sender_type,
                            'last_commented_by' => $sender_id,
                            'last_updated'      => date('Y-m-d H:i:s'),
                            'reason'            => $request->reason,
                            'type'              => $dispute_detail->next_type,
                            'updated'           => date('Y-m-d H:i:s'),
                            'created'           => date('Y-m-d H:i:s'),
                        ];

                        $isDisputed     = \Models\RaiseDispute::submit($raiseArray);
                        $dispute_detail = \Models\RaiseDispute::detail($project_id,$sender_id);

                        if(auth()->user()->type == 'employer'){
                            $isNotified = \Models\Notifications::notify(
                                $project->proposal->talent->id_user,
                                $project->company_id,
                                'JOB_RAISE_DISPUTE_RECEIVED',
                                json_encode([
                                    "sender_id"     => (string) $project->company_id,
                                    "receiver_id"   => (string) $project->proposal->talent->id_user,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );
                        }else{
                            $isNotified = \Models\Notifications::notify(
                                $project->company_id,
                                $project->proposal->talent->id_user,
                                'JOB_RAISE_DISPUTE_RECEIVED',
                                json_encode([
                                    "sender_id"     => (string) $project->proposal->talent->id_user,
                                    "receiver_id"   => (string) $project->company_id,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );
                        }
                    }else{
                        $isUpdated = \Models\RaiseDispute::where('id_raised_dispute',$dispute_detail->id_raised_dispute)->update(['type' => $dispute_detail->next_type,'last_commented_by' => $sender_id,'last_updated' => date('Y-m-d H:i:s'),'updated' => date('Y-m-d H:i:s')]);  
                        $isDisputed = $dispute_detail->id_raised_dispute;

                        if(auth()->user()->type == 'employer'){
                            $isNotified = \Models\Notifications::notify(
                                $project->proposal->talent->id_user,
                                $project->company_id,
                                'JOB_RAISE_DISPUTE_RECEIVED_REPLY',
                                json_encode([
                                    "sender_id"     => (string) $project->company_id,
                                    "receiver_id"   => (string) $project->proposal->talent->id_user,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );
                        }else{
                            $isNotified = \Models\Notifications::notify(
                                $project->company_id,
                                $project->proposal->talent->id_user,
                                'JOB_RAISE_DISPUTE_RECEIVED_REPLY',
                                json_encode([
                                    "sender_id"     => (string) $project->proposal->talent->id_user,
                                    "receiver_id"   => (string) $project->company_id,
                                    "project_id"    => (string) $project->id_project
                                ])
                            );
                        }
                    }

                    $commentArray = [
                        'dispute_id'    => $isDisputed,
                        'sender_id'     => $sender_id,
                        'comment'       => $request->comment,
                        'type'          => $next_type,
                        'updated'       => date('Y-m-d H:i:s'),
                        'created'       => date('Y-m-d H:i:s'),
                    ];

                    $isCommentCreated = \Models\RaiseDisputeComments::submit($commentArray);
                    
                    if(!empty($request->dispute_documents)){
                        \Models\File::whereIn('id_file',explode(",", str_replace(" ", "", $request->dispute_documents)))->where('user_id',$sender_id)->where('type','disputes')->update(['record_id' => $isCommentCreated]);
                    }

                    if(!empty($isDisputed)){
                        $this->status   = true;
                        $this->message  = trans("general.M0484");
                        $this->redirect = url(sprintf("%s/project/details?job_id=%s",auth()->user()->type,___encrypt($project_id)));
                    }else{
                        $this->message = trans("general.M0121");
                    }

                    $this->jsondata = \Models\RaiseDispute::where('id_raised_dispute',$isDisputed);
                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'employer',
                        'action'            => 'raise-dispute',
                        'reference_type'    => 'project',
                        'reference_id'      => $project_id
                    ]));
                }else{
                    $this->jsondata = (object)['comment' => trans('general.M0564')];
                }
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'nomessage' => true,
                'redirect'  => $this->redirect,
            ]);            
        }
   	}
