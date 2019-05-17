<?php 
    namespace App\Http\Controllers\Front;

    use App\Http\Requests;
    use Illuminate\Support\Facades\DB;
    use App\Models\Proposals;
    use App\Http\Controllers\Controller;
    
    use Illuminate\Support\Facades\Cookie;
    use Illuminate\Validation\Rule;
    use Illuminate\Http\Request;
    use Yajra\Datatables\Html\Builder;
    use Crypt;

    use App\Models\Interview as Interview;
    
    class PortfolioController extends Controller {

        private $jsondata;
        private $redirect;
        private $message;
        private $status; 
        private $prefix;
        private $language;

        public function __construct(){
            $this->jsondata     = [];
            $this->message      = false;
            $this->redirect     = false;
            $this->status       = false;
            $this->prefix       = \DB::getTablePrefix();
            $this->language     = \App::getLocale();
            \View::share ( 'footer_settings', \Cache::get('configuration') );
        }

        /**
         * [This method is used for randering view of Portfolio] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function view_portfolio(Request $request, Builder $htmlBuilder){
            $data['title']                  = trans('website.W0462');
            $data['subheader']              = 'talent.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'talent.portfolio.view';

            $data['user']                   = \Models\Talents::get_user(\Auth::user());
            $data['submenu']                = 'portfolio';

            $data['button']                 = '<a href="'.url('talent/profile/portfolio/add').'"><img src="'.asset('images/add.png').'"></a>';

            // $data['get_file']               = \Models\Portfolio::get_portfolio(\Auth::user()->id_user); 

            if ($request->ajax()) {

                $table_portfolio = \DB::table('talent_portfolio as portfolio');
                $keys = [
                    'portfolio.id_portfolio',
                    'portfolio.portfolio',
                    'portfolio.description',
                    'portfolio.created',
                ];

                $table_portfolio->select($keys);
                $table_portfolio->where('portfolio.user_id', '=', \Auth::user()->id_user);
                $table_portfolio->groupBy(['portfolio.id_portfolio']);
                $table_portfolio->orderBy('portfolio.id_portfolio','DESC');
                
                $result = $table_portfolio->get();

                foreach ($result as &$item) {
                    $table_files = \DB::table('files');
                    $table_files->select(['id_file','filename','folder','extension']);
                    $table_files->where('files.record_id',$item->id_portfolio);
                    $table_files->where('files.type','portfolio');
                    $table_files->orderBy('files.id_file','DESC');

                    $item->file = json_decode(json_encode($table_files->get()),true);
                }


                return \Datatables::of($result)
                ->editColumn('portfolio',function($item){
                    $html = '<div class="content-box find-job-listing clearfix" style="padding: 10px 20px;">';
                        $html .= '<div class="find-job-left no-border">';
                            $html .= '<div class="content-box-header clearfix">';
                                $html .= '<div class="contentbox-header-title">';
                                    $html .= '<h3><a href="'.url(sprintf("%s/profile/portfolio/view?portfolio_id=%s",TALENT_ROLE_TYPE,___encrypt($item->id_portfolio))).'">'.$item->portfolio.'</a></h3>';

                                    $html .= '<span class="text-grey">'.trans('website.W0690').' '.___d($item->created).'</span>';
                                    $html .= '<span class="pull-right" style="position:relative;top:-5px;">';                   
                                    $html .= '<a href="'.url(sprintf("%s/profile/portfolio/edit?portfolio_id=%s",TALENT_ROLE_TYPE,___encrypt($item->id_portfolio))).'" class="btn btn-primary btn-small m-l-n">'.'Edit'.'</a>';

                                    $html .='<a href="javascript:void(0)" class="btn btn-primary btn-small m-l-n" data-url="'.sprintf(url('ajax/%s?id_portfolio=%s'), DELETE_PORTFOLIO, $item->id_portfolio ).'" data-toremove="portfolio" title="Delete" data-request="delete" data-file_id="'.$item->id_portfolio.'" data-delete-id="file_id" data-edit-id="file_id" data-single="true" data-after-upload=".single-remove" data-ask="Do you really want to delete the document?">Remove</a>';

                                    $html .= '</span>';
                                $html .= '</div>';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</div>';

                    return $html;
                })
                ->make(true);
            }

            $data['html'] = $htmlBuilder
            ->parameters(["dom" => "<'row' <'col-md-6 table-heading'> <'col-md-3'> <'col-md-3 filter-option'>> rt <'row'<'col-md-6'><'col-md-6'p> >"])
            ->addColumn(['data' => 'portfolio', 'name' => 'portfolio', 'title' => '&nbsp;', 'width' => '0', 'searchable' => false, 'orderable' => false]);


            return view('talent.portfolio.index')->with($data);
        }

        /**
         * [This method is used for randering view of add portfolio] 
         * @param  null
         * @return \Illuminate\Http\Response
         */
        
        public function addportfolio(){
            $data['title']                  = trans('website.W0463');
            $data['subheader']              = 'talent.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'talent.portfolio.add';

            $data['user']                   = \Models\Talents::get_user(\Auth::user());
            $data['submenu']                = 'portfolio';
            return view('talent.portfolio.index')->with($data);
        }

        /**
         * [This method is used for Edit of Portfolio]
         * @param  Request
         * @return \Illuminate\Http\Response
         */
        
        public function editportfolio(Request $request){
            if(!empty($request->portfolio_id)){
                $portfolio_id = ___decrypt($request->portfolio_id);
            }else{
                return redirect(url(sprintf('%s/portfolio',TALENT_ROLE_TYPE)));
            }
            $data['title']                  = trans('website.W0464');
            $data['subheader']              = 'talent.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'talent.portfolio.edit';

            $data['user']                   = \Models\Talents::get_user(\Auth::user());
            $data['portfolio']              = \Models\Portfolio::get_portfolio(\Auth::user()->id_user," id_portfolio = {$portfolio_id} ",'single');
            $data['submenu']                = 'portfolio';
            return view('talent.portfolio.index')->with($data);
        }

        /**
         * [This method is used for Portfolio View]
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function singleportfolio(Request $request){
            if(!empty($request->portfolio_id)){
                $portfolio_id = ___decrypt($request->portfolio_id);
            }else{
                return redirect(url(sprintf('%s/portfolio',TALENT_ROLE_TYPE)));
            }

            $data['title']                  = trans('website.W0462');
            $data['subheader']              = 'talent.includes.top-menu';
            $data['header']                 = 'innerheader';
            $data['footer']                 = 'innerfooter';
            $data['view']                   = 'talent.portfolio.single';

            $data['user']                   = \Models\Talents::get_user(\Auth::user());
            $data['portfolio']              = \Models\Portfolio::get_portfolio(\Auth::user()->id_user," id_portfolio = {$portfolio_id} ",'single');
            $data['submenu']                = 'portfolio';
            return view('talent.portfolio.index')->with($data);
        }

                /**
         * [This method is used for document Curriculum Vitae ]
         * @param  Request
         * @return Json Response
         */
        
        public function portfolia_save_document(Request $request){
            $validator = \Validator::make($request->all(), [
                "file"            => validation('document'),
            ],[
                'file.validate_file_type'  => trans('general.M0119'),
            ]);
            if($validator->passes()){
                $certificates  = \Models\Talents::get_user(\Auth::user())['certificate_attachments'];
                if( count($certificates) < 20){
                    $folder = 'uploads/certificates/';
                    $uploaded_file = upload_file($request,'file',$folder);
                    
                    $data = [
                        'user_id' => \Auth::user()->id_user,
                        'record_id' => \Auth::user()->id_user,
                        'reference' => 'users',
                        'filename' => $uploaded_file['filename'],
                        'extension' => $uploaded_file['extension'],
                        'folder' => $folder,
                        'type' => 'portfolio',
                        'size' => $uploaded_file['size'],
                        'is_default' => DEFAULT_NO_VALUE,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s'),
                    ];

                    $isInserted = \Models\Talents::create_file($data,true,true);
                    
                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'talent',
                        'action'            => 'talent-step-three document',
                        'reference_type'    => 'users',
                        'reference_id'      => \Auth::user()->id_user
                    ]));
                    
                    if(!empty($isInserted)){
                        if(!empty($isInserted['folder'])){
                            $isInserted['file_url'] = url(sprintf("%s/%s",$isInserted['folder'],$isInserted['filename']));
                        }
                        
                        $url_delete = sprintf(
                            url('ajax/%s?id_file=%s'),
                            DELETE_DOCUMENT,
                            $isInserted['id_file']
                        );

                        $this->jsondata = sprintf(NEW_PORTFOLIO_TEMPLATE,
                            $isInserted['id_file'],
                            url(sprintf('/download/file?file_id=%s',___encrypt($isInserted['id_file']))),
                            asset('/'),
                            substr($uploaded_file['filename'], 0,3),
                            $uploaded_file['filename'],
                            $url_delete,
                            $isInserted['id_file'],
                            asset('/'),
                            $isInserted['id_file']
                        );
                        
                        $this->status = true;
                        $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
                    }
                }else{
                    $this->jsondata = (object)['file' => trans('general.M0563')];
                }
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }

         /**
         * [This method is used for Portfolio Image]
         * @param  Request
         * @return Json Response
         */

        // public function portfolio_image(Request $request){
        //     $validator = \Validator::make($request->all(), [
        //         "file"                      => ['required','validate_image_type'],
        //     ],[
        //         'file.validate_image_type'   => trans('general.M0120'),
        //     ]);
            
        //     if($validator->passes()){
        //         $folder = 'uploads/portfolio/';
        //         $uploaded_file = upload_file($request,'file',$folder,true);
        //         $data = [
        //             'user_id' => $request->user()->id_user,
        //             'record_id' => "",
        //             'reference' => 'users',
        //             'filename' => $uploaded_file['filename'],
        //             'extension' => $uploaded_file['extension'],
        //             'folder' => $folder,
        //             'type' => 'portfolio',
        //             'size' => $uploaded_file['size'],
        //             'is_default' => DEFAULT_NO_VALUE,
        //             'created' => date('Y-m-d H:i:s'),
        //             'updated' => date('Y-m-d H:i:s'),
        //         ];
        //         $isInserted = \Models\Talents::create_file($data,true,true);
                
        //         /* RECORDING ACTIVITY LOG */
        //         event(new \App\Events\Activity([
        //             'user_id'           => \Auth::user()->id_user,
        //             'user_type'         => 'talent',
        //             'action'            => 'talent-add-portfolio-image',
        //             'reference_type'    => 'users',
        //             'reference_id'      => \Auth::user()->id_user
        //         ]));

        //         if(!empty($isInserted)){
        //             if(!empty($isInserted['folder'])){
        //                 $isInserted['file_url'] = url(sprintf("%s/%s",$isInserted['folder'],$isInserted['filename']));
        //             }
                    
        //             $url_delete = sprintf(
        //                 url('ajax/%s?id_file=%s'),
        //                 DELETE_DOCUMENT,
        //                 $isInserted['id_file']
        //             );

        //             $this->jsondata = sprintf(PORTFOLIO_TEMPLATE,
        //                 $isInserted['id_file'],
        //                 asset(sprintf("%s%s",$isInserted['folder'],$isInserted['filename'])),
        //                 asset(sprintf("%s%s%s",$isInserted['folder'],'thumbnail/',$isInserted['filename'])),
        //                 $url_delete,
        //                 $isInserted['id_file'],
        //                 trans('website.W0454'),
        //                 asset('/'),
        //                 $isInserted['id_file']
        //             );
                    
        //             $this->status = true;
        //             $this->message  = sprintf(ALERT_SUCCESS,trans("general.M0110"));
        //         }
        //     }else{
        //         $this->jsondata = ___error_sanatizer($validator->errors());
        //     }

        //     return response()->json([
        //         'data'      => $this->jsondata,
        //         'status'    => $this->status,
        //         'message'   => $this->message,
        //         'redirect'  => $this->redirect,
        //     ]);
        // }

        /**
         * [This method is used for handle add Portfolio]
         * @param  Request
         * @return Json Response
         */

        public function __addportfolio(Request $request){

            // dd('hello',$request->all());

            $validator = \Validator::make($request->all(), [
                "portfolio"        => validation('portfolio'),
                "description"      => validation('portfolio_description'),
                "portfolio_docs"   => ['required'],
            ],[
                'portfolio.required'        => trans('general.M0312'),
                'portfolio.string'          => trans('general.M0304'),
                'portfolio.regex'           => trans('general.M0304'),
                'portfolio.max'             => trans('general.M0305'),
                'portfolio.min'             => trans('general.M0306'),
                'description.required'      => trans('general.M0303'),
                'description.string'        => trans('general.M0307'),
                'description.regex'         => trans('general.M0307'),
                'description.max'           => trans('general.M0308'),
                'description.min'           => trans('general.M0309'),
                'portfolio_docs.required'   => 'Project document field is required.',
            ]);

            if($validator->passes()){
                $insertArr = [
                    'user_id' => \Auth::user()->id_user,
                    'portfolio' => $request->portfolio,
                    'description' => $request->description,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => date('Y-m-d H:i:s')
                ];

                if(!empty($request->portfolio_id)){
                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'talent',
                        'action'            => 'talent-update-portfolio',
                        'reference_type'    => 'users',
                        'reference_id'      => \Auth::user()->id_user
                    ]));
                    $portfolio_id = \Models\Portfolio::save_portfolio($insertArr,$request->portfolio_id);
                }else{
                    /* RECORDING ACTIVITY LOG */
                    event(new \App\Events\Activity([
                        'user_id'           => \Auth::user()->id_user,
                        'user_type'         => 'talent',
                        'action'            => 'talent-add-portfolio',
                        'reference_type'    => 'users',
                        'reference_id'      => \Auth::user()->id_user
                    ]));

                    $portfolio_id = \Models\Portfolio::save_portfolio($insertArr);
                }
                
                if(!empty($portfolio_id)){
                    $file_ids = (array) explode(",", $request->portfolio_docs);
                    \Models\File::update_file($file_ids,['record_id' => $portfolio_id]);
                    \Models\Talents::delete_file(sprintf(" record_id = 0 AND type = 'portfolio' AND  user_id = %s", \Auth::user()->id_user));

                    if(!empty($request->removed_portfolio)){
                        \Models\Talents::delete_file(sprintf(" id_file IN(%s) AND  user_id = %s",$request->removed_portfolio,\Auth::user()->id_user));                        
                    }
                }
                $this->redirect = url(sprintf('%s/profile/portfolio',TALENT_ROLE_TYPE));
                $this->status = true;
                $this->message = trans("website.W0323");
            }else{
                $this->jsondata = ___error_sanatizer($validator->errors());
            }

            return response()->json([
                'data'      => $this->jsondata,
                'status'    => $this->status,
                'message'   => $this->message,
                'redirect'  => $this->redirect,
            ]);
        }
    }