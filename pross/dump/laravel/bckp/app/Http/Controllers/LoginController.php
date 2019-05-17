<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Session;
use Cookie;
use DB;

class LoginController extends Controller
{
    public function index()
    {
        $data['page_title'] = '360 Reference';
        $data['redirect'] = '';

        if (\Request::has('redirect')){
            $data['redirect'] = \Request::get('redirect');
        }
        if (\Request::has('plan')){
            $data['redirect'] = url('subscribe-plan/'.\Request::get('acid').'/'.\Request::get('id'));
        }
        
        if (\Auth::check() ){
            
            $newly_added_user = DB::table('newly_added_user')->where('new_user_id',\Auth::user()->id)->first();
           
            if($newly_added_user && $newly_added_user->status=="pending"){
                \Auth::logout();
                return view('front.login')->with($data);
            }

            if(\Auth::user()->role_id == '2')
            {

                return redirect('individual/individual-dashboard');
            }

            if(\Auth::user()->role_id == '1')
            {
                return redirect('admin/dashboard');
            }

            if(\Auth::user()->role_id == '3')
            {
                return redirect('company/company-dashboard');
            }
        }
    	
    	return view('front.login')->with($data);
    }

    public function validate_login(Request $request){

        // $this->validate($request, [
        //             'email' => ['required','email'],
        //             'password' => 'required|min:6',
        //     ],
        //     [
        //             'email.required' => 'Please enter Email1.',
        //             'password.required' => 'Please enter Password.',
        //             'password.min' => 'The password must be atleast 6 characters.',
        //             'email.email' => 'Please enter valid email address.'
        // ]);
    	$validator = \Validator::make($request->all(), [
                        'email' => ['required','email'],
                        'password' => 'required|min:6',
                ],
                [
                        'email.required' => 'Please enter Email123333ss.',
                        'password.required' => 'Please enter Password.',
                        'password.min' => 'The password must be atleast 6 characters.',
                        'email.email' => 'Please enter valid email address.'
                ]
        );
        // return response()->json('Project created!');
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()],422);
        } 
        else {

            if (\Auth::attempt(['email' => $request->email, 'password' => $request->password],true)) {
                return response()->json(['success'],200);
            }else {
                return response()->json(['errors'=>['password'=>['0'=>'Your account is not registered']]],422);
            }
        }
        // return redirect('/login');
    }
}
