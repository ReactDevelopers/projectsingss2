<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Lib\General;
use DB;

class EmailController extends Controller {
    
    /**
     * Getting the email template by id
     * @param  Request $request [type]
     * @return Json
     */
    public function get(Request $request,$type)
    {
        $this->data = \App\Models\EmailTemplate::select('body','subject','type')->where('type',$type)->first();
        return $this->response();
    }

    /**
     * Updating the email template.
     * @param  Request $request [type]
     * @return [Json] 
     */
    public function update(Request $request,$type)
    {
        $validator = \Validator::make($request->all(),[
            'body'=>'required',
            'subject' =>'required'
        ]);

        if($validator->fails()){

            $this->status = false;
            $this->message = 'Please enter the valid details.';
            $this->errors = $validator->errors();
            $this->code = 422;

        }else{

            $this->message = 'Email Template has been updated.';
            $data = $request->only(['body', 'subject']);
            \App\Models\EmailTemplate::where('type', $type)->update($data);
            $this->code = 200;
        }

        return $this->response($this->code);
    }

}
