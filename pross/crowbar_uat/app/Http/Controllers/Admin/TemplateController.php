<?php

    namespace App\Http\Controllers\Admin;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\Controller;
    use App\Models\Templates;
    use Illuminate\Validation\Rule;
    use Yajra\Datatables\Datatables;

    class TemplateController extends Controller{
        
        /**
         * [This method is used for randering view of index] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function index(){
            $data['page_title'] = 'Message Configuration';
            $data['messageconfig'] = Templates::all();
            return view('backend.templates.list')->with($data);
        }

        /**
         * [This method is used for creation] 
         * @param  null
         * @return \Illuminate\Http\Response
         */

        public function create(){
            $data['page_title'] = 'Add Message';
            
            return view('admin.forms.add-message-config')->with($data);
        }

        /**
         * [This method is used for store] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function store(Request $request){
            $validate = $this->validate($request, [
                'title' => 'required|unique:message_config|regex:/(^[a-z_]+$)+/',
                'subject' => 'required|regex:/(^[A-Za-z0-9 ]+$)+/',
                'content' => 'required'
            ]);
            
            DB::table('message_config')->insert(
                [
                    'message_type' => $request['message_type'], 
                    'title' => $request['title'], 
                    'subject' => (string)$request['subject'],
                    'content'=> (string)$request['content'],
                    'status'=>(string)$request['status'],
                    'created_at'=>date('Y-m-d H:i:s')
                ]
            );
            $request->session()->flash('success', 'Message saved successfully.');
            return redirect('admin/message-config');
        }

        /**
         * [This method is used for show] 
         * @param  Id
         * @return \Illuminate\Http\Response
         */

        public function show($id){
            $data = Templates::rows(
                array(
                    'id',
                    'title',
                    'alias',
                    'subject',
                    'status',
                    'id as action'
                )
            );

            return Datatables::of($data)->make(true);            
        }

        /**
         * [This method is used for edit] 
         * @param  Id
         * @return \Illuminate\Http\Response
         */

        public function edit($id){
            $data['page_title'] = 'Edit Message';
            $data['message'] = Templates::where('id',$id)->first();
            $data['id'] = $id;
            return view('admin.forms.edit-message-config')->with($data);
        }

        /**
         * [This method is used for update] 
         * @param  Request
         * @return \Illuminate\Http\Response
         */

        public function update(Request $request, $id){
            $this->validate($request, [
                'subject' => [
                    'required',
                    Rule::unique('message_config')->ignore($id),
                    'regex:/(^[A-Za-z0-9 ]+$)+/'
                ],
                'content' => ['required']
            ]);

            DB::table('message_config')
                ->where('id', $id)
                ->update([
                    'subject' => $request['subject'],
                    'content'=>(string)$request['content'],
                    'status'=>$request['status']
                ]);

            $request->session()->flash('success', 'Message updated successfully.');
            return redirect('admin/message-config');
        }

        /**
         * [This method is used for destroy] 
         * @param Id
         * @return null
         */

        public function destroy($id){
            DB::table('message_config')->where('id', $id)->delete();
            $request->session()->flash('success', 'Message deleted successfully.');
        }
    }