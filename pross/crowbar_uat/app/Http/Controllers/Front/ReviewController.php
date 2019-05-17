<?php
    namespace App\Http\Controllers\Front;

    use App\Http\Requests;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Illuminate\Support\Facades\DB;

    use App\Http\Controllers\Controller;

    class ReviewController extends Controller{
    
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

            $this->head_message = "";

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
         * [This method is used for profile review in detail]
         * @param  Request
         * @return Json\Response
         */ 

	    public function review_detail(Request $request){
            $request->replace($this->post);
	        
	        if(empty($request->review_id)){
	            $this->message = "M0121";
	            $this->error = sprintf(trans(sprintf('general.%s',$this->message)),'review_id'); 
	        }else{
                $this->status = true;
	        	$review = \Models\Reviews::review_detail($request->review_id);
                $this->jsondata = api_resonse_common($review,true);
	        }

	        return response()->json(
	            $this->populateresponse([
	                'status' => $this->status,
	                'data' => $this->jsondata
	            ])
	        );             
    	}

        /**
         * [This method is used for Ratings submission]
         * @param  Request
         * @return Json Response
         */        

        public function submit_review_talent(Request $request){
            $this->jsondata = (object)[];
            $project_id = ___decrypt($request->job_id);

            $prefix                 = DB::getTablePrefix();
            $language               = \App::getLocale();
            $user                   = (object)['id_user' => \Auth::user()->id_user];
            
            if($request->ajax()){
                $validator = \Validator::make($request->all(), [
                    "description"           => validation('review_description'),
                    "category_two"          => validation('review_performance'),
                    "category_three"        => validation('review_punctuality'),
                    "category_four"         => validation('review_quality'),
                    "category_five"         => validation('review_skill'),
                    "category_six"          => validation('review_support'),
                ],[
                    "description.required"          => trans('general.M0343'),
                    "description.string"            => trans('general.M0336'),
                    "description.regex"             => trans('general.M0336'),
                    "description.max"               => trans('general.M0328'),
                    "description.min"               => trans('general.M0327'),
                    "category_one.required"         => trans('general.M0330'),
                    "category_one.min"              => trans('general.M0330'),
                    "category_one.numeric"          => trans('general.M0337'),
                    "category_two.required"         => trans('general.M0331'),
                    "category_two.min"              => trans('general.M0331'),
                    "category_two.numeric"          => trans('general.M0338'),
                    "category_three.required"       => trans('general.M0332'),
                    "category_three.min"            => trans('general.M0332'),
                    "category_three.numeric"        => trans('general.M0339'),
                    "category_four.required"        => trans('general.M0333'),
                    "category_four.min"             => trans('general.M0333'),
                    "category_four.numeric"         => trans('general.M0340'),
                    "category_five.required"        => trans('general.M0334'),
                    "category_five.min"             => trans('general.M0334'),
                    "category_five.numeric"         => trans('general.M0341'),
                    "category_six.required"         => trans('general.M0335'),
                    "category_six.min"              => trans('general.M0335'),
                    "category_six.numeric"          => trans('general.M0342'),
                ]);

                if($validator->passes()){
                    $project        = \Models\Projects::defaultKeys()->where('id_project',$project_id)->get()->first();
                    $review         = \Models\Reviews::where(['project_id' => $project_id, 'sender_id' => \Auth::user()->id_user ])->get()->first();
                    
                    if(!empty($review->id_review)){
                        $this->message = sprintf(ALERT_DANGER,trans("general.M0329"));
                    }else{
                        $total_average  = (($request->category_two+$request->category_three+$request->category_four+$request->category_five+$request->category_six)/25)*5;
                        $reviewArray = [
                            'project_id'            =>  $project_id,
                            'sender_id'             =>  \Auth::user()->id_user,
                            'receiver_id'           =>  $project->company_id,
                            'description'           =>  $request->description,
                            'review_average'        =>  $total_average,
                            'category_two'          =>  $request->category_two,    
                            'category_three'        =>  $request->category_three,
                            'category_four'         =>  $request->category_four,
                            'category_five'         =>  $request->category_five,
                            'category_six'          =>  $request->category_six,
                            'created'               =>  date('Y-m-d h:i:s'),
                            'updated'               =>  date('Y-m-d h:i:s'),
                        ];

                        $isInserted = \Models\Reviews::add_review($reviewArray);
                        
                        $isNotified = \Models\Notifications::notify(
                            $project->company_id,
                            $request->user()->id_user,
                            'JOB_REVIEW_REQUEST_BY_TALENT',
                            json_encode([
                                'review_id'     => (string) $isInserted,
                                'sender_id'     => (string) auth()->user()->id_user,
                                'receiver_id'   => (string) $project->company_id,
                                'project_id'    => (string) $project_id,
                            ])
                        );

                        if($isInserted){
                            $this->status   = true;
                            $this->message  = trans("general.M0326");
                            $this->head_message  = trans("general.M0599");
                            $this->redirect = url(sprintf('%s/project/submit/reviews?job_id=%s',$request->user()->type,___encrypt($project_id)));
                        }
                    }               
                }else{
                    $errors = json_decode(json_encode($validator->errors()),true);
                    if(!empty($errors['category_two']) || !empty($errors['category_three']) || !empty($errors['category_four']) || !empty($errors['category_five']) || !empty($errors['category_six'])){
                        unset($errors['category_two']);
                        unset($errors['category_three']);
                        unset($errors['category_four']);
                        unset($errors['category_five']);
                        unset($errors['category_six']);

                        $errors['rating'] = trans('general.M0345');
                        $this->jsondata = (object)$errors;
                    }else{
                        $this->jsondata = ___error_sanatizer($validator->errors());
                    }
                }

                return response()->json([
                    'data'         => $this->jsondata,
                    'status'       => $this->status,
                    'message'      => $this->message,
                    'head_message' => $this->head_message,
                    'redirect'     => $this->redirect,
                ]);
            }else{
                $data['subheader']      = 'talent.includes.top-menu';
                $data['header']         = 'innerheader';
                $data['footer']         = 'innerfooter';
                $data['view']           = 'talent.review.submit';
                
                $data['user']           = \Models\Employers::get_user($request->user());
                $data['project']        = \Models\Projects::defaultKeys()->with([
                    'reviews' => function($q){
                        $q->where('sender_id',auth()->user()->id_user);
                    },
                    'employer' => function($q) use($language,$prefix,$user){
                        $q->select(
                            'id_user',
                            'company_name',
                            'contact_person_name',
                            'company_website',
                            'company_work_field',
                            'company_biography',
                            \DB::Raw("YEAR({$prefix}users.created) as member_since"),
                            \DB::Raw("IF({$prefix}users.last_name IS NULL,{$prefix}users.first_name, CONCAT({$prefix}users.first_name, ' ',{$prefix}users.last_name)) AS name")
                        );

                        $q->isTalentSavedEmployer($user->id_user);
                        $q->companyLogo();
                        $q->country();
                        $q->review();
                        $q->totalHirings();
                        $q->withCount([
                            'reviews',
                            'projects' => function($q){
                                $q->whereNotIn('projects.status',['draft','trashed']);
                            }
                        ]);
                    }
                ])
                ->where('id_project',$project_id)
                ->get()
                ->first();

                $data['title']          = $data['project']->title;
                
                if(!empty($data['project']->reviews)){
                    $data['view']       = 'talent.review.view';
                }
                
                return view('talent.review.index')->with($data);
            }
        }

        /**
         * [This method is used for showing Talent's Reviews][New]
         * @param  Request
         * @return Json Response
         */

        public function received_review_talent(Request $request){

            $data['subheader']      = 'talent.includes.top-menu';
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['view']           = 'talent.review.received';

            $project_id = ___decrypt($request->job_id);
                
            $data['user']           = \Models\Employers::get_user($request->user());
            $data['project']        = \Models\Projects::defaultKeys()->with([
                'reviews' => function($q){
                    $q->where('receiver_id',auth()->user()->id_user);
                }
            ])
            ->where('id_project',$project_id)
            ->get()
            ->first();

            $data['title']          = $data['project']->title;
                    
            return view('talent.review.index')->with($data);
        }
        

        /**
         * [This method is used for Ratings submission]
         * @param  Request
         * @return Json Response
         */        

        public function submit_review_employer(Request $request){
            $this->jsondata = (object)[];
            $project_id = ___decrypt($request->job_id);
            
            if($request->ajax()){
                $validator = \Validator::make($request->all(), [
                    "description"           => validation('review_description'),
                    "category_two"          => validation('review_performance'),
                    "category_three"        => validation('review_punctuality'),
                    "category_four"         => validation('review_quality'),
                    "category_five"         => validation('review_skill'),
                    "category_six"          => validation('review_support'),
                ],[
                    "description.required"          => trans('general.M0343'),
                    "description.string"            => trans('general.M0336'),
                    "description.regex"             => trans('general.M0336'),
                    "description.max"               => trans('general.M0328'),
                    "description.min"               => trans('general.M0327'),
                    "category_one.required"         => trans('general.M0330'),
                    "category_one.min"              => trans('general.M0330'),
                    "category_one.numeric"          => trans('general.M0337'),
                    "category_two.required"         => trans('general.M0331'),
                    "category_two.min"              => trans('general.M0331'),
                    "category_two.numeric"          => trans('general.M0338'),
                    "category_three.required"       => trans('general.M0332'),
                    "category_three.min"            => trans('general.M0332'),
                    "category_three.numeric"        => trans('general.M0339'),
                    "category_four.required"        => trans('general.M0333'),
                    "category_four.min"             => trans('general.M0333'),
                    "category_four.numeric"         => trans('general.M0340'),
                    "category_five.required"        => trans('general.M0334'),
                    "category_five.min"             => trans('general.M0334'),
                    "category_five.numeric"         => trans('general.M0341'),
                    "category_six.required"         => trans('general.M0335'),
                    "category_six.min"              => trans('general.M0335'),
                    "category_six.numeric"          => trans('general.M0342'),
                ]);

                if($validator->passes()){
                    $project   = \Models\Projects::defaultKeys()->where('id_project',$project_id)->with([
                        'proposal' => function($q){
                            $q->defaultKeys()->where('talent_proposals.status','accepted')->with([
                                'talent' => function($q){
                                    $q->defaultKeys();
                                }
                            ]);
                        }
                    ])
                    ->get()
                    ->first();

                    $review    = \Models\Reviews::where(['project_id' => $project_id, 'sender_id' => \Auth::user()->id_user ])->get()->first();
                    
                    if(!empty($review->id_review)){
                        $this->message = sprintf(ALERT_DANGER,trans("general.M0329"));
                    }else{
                        $total_average  = (($request->category_two+$request->category_three+$request->category_four+$request->category_five+$request->category_six)/25)*5;
                        
                        $reviewArray = [
                            'project_id'            =>  $project_id,
                            'sender_id'             =>  \Auth::user()->id_user,
                            'receiver_id'           =>  $project->proposal->talent->id_user,
                            'description'           =>  $request->description,
                            'review_average'        =>  $total_average,
                            'category_two'          =>  $request->category_two,    
                            'category_three'        =>  $request->category_three,
                            'category_four'         =>  $request->category_four,
                            'category_five'         =>  $request->category_five,
                            'category_six'          =>  $request->category_six,
                            'created'               =>  date('Y-m-d h:i:s'),
                            'updated'               =>  date('Y-m-d h:i:s'),
                        ];

                        $isInserted = \Models\Reviews::add_review($reviewArray);
                        
                        $isNotified = \Models\Notifications::notify(
                            $project->proposal->talent->id_user,
                            $request->user()->id_user,
                            'JOB_REVIEW_REQUEST_BY_EMPLOYER',
                            json_encode([
                                'review_id'     => (string) $isInserted,
                                'project_id'    => (string) $project_id,
                                'received_id'   => $project->proposal->talent->id_user,
                                'sender_id'     => $request->user()->id_user
                            ])
                        );

                        if($isInserted){
                            $this->status   = true;
                            $this->message  = trans("general.M0326");
                            $this->head_message  = trans("general.M0599");
                            $this->redirect = url(sprintf('%s/project/submit/reviews?job_id=%s',$request->user()->type,___encrypt($project_id)));
                        }
                    }               
                }else{
                    $errors = json_decode(json_encode($validator->errors()),true);
                    if(!empty($errors['category_two']) || !empty($errors['category_three']) || !empty($errors['category_four']) || !empty($errors['category_five']) || !empty($errors['category_six'])){
                        unset($errors['category_two']);
                        unset($errors['category_three']);
                        unset($errors['category_four']);
                        unset($errors['category_five']);
                        unset($errors['category_six']);

                        $errors['rating'] = trans('general.M0345');
                        $this->jsondata = (object)$errors;
                    }else{
                        $this->jsondata = ___error_sanatizer($validator->errors());
                    }
                }

                return response()->json([
                    'data'      => $this->jsondata,
                    'status'    => $this->status,
                    'message'   => $this->message,
                    'head_message'   => $this->head_message,
                    'redirect'  => $this->redirect,
                ]);
            }else{
                $data['subheader']      = 'employer.includes.top-menu';
                $data['header']         = 'innerheader';
                $data['footer']         = 'innerfooter';
                $data['view']           = 'employer.review.submit';
                
                $data['user']           = \Models\Employers::get_user($request->user());
                $data['project']        = \Models\Projects::defaultKeys()->with([
                    'reviews' => function($q){
                        $q->where('sender_id',auth()->user()->id_user);
                    },
                    'proposal' => function($q){
                        $q->defaultKeys()->where('talent_proposals.status','accepted')->with([
                            'talent' => function($q){
                                $q->defaultKeys()->country()->review()->with([
                                    'interests'
                                ]);
                            }
                        ]);
                    }
                ])
                ->where('id_project',$project_id)
                ->get()
                ->first();

                $data['title']          = $data['project']->title;
                
                if(!empty($data['project']->reviews)){
                    $data['view']       = 'employer.review.view';
                }
                
                return view('employer.jobdetail.index')->with($data);
            }
        }

        /**
         * [This method is used for showing Employer's Reviews][New]
         * @param  Request
         * @return Json Response
         */

        public function received_review_employer(Request $request){

            $data['subheader']      = 'employer.includes.top-menu';
            $data['header']         = 'innerheader';
            $data['footer']         = 'innerfooter';
            $data['view']           = 'employer.review.received';

            $project_id = ___decrypt($request->job_id);
                
            $data['user']           = \Models\Employers::get_user($request->user());
            $data['project']        = \Models\Projects::defaultKeys()->with([
                'reviews' => function($q){
                    $q->where('receiver_id',auth()->user()->id_user);
                }
            ])
            ->where('id_project',$project_id)
            ->get()
            ->first();

            $data['title']          = $data['project']->title;
                    
            return view('employer.jobdetail.index')->with($data);

        }

   	}