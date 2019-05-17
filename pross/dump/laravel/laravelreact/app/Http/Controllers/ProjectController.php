<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('is_completed', false)
                            ->orderBy('created_at', 'desc')
                            ->withCount(['tasks' => function ($query) {
                                $query->where('is_completed', false);
                            }])
                            ->get();

        return $projects->toJson();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $project = Project::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return response()->json('Project created!');
    }

    public function show($id)
    {
        $project = Project::with(['tasks' => function ($query) {
            $query->where('is_completed', false);
        }])->find($id);

        return $project->toJson();
    }

    public function markAsCompleted(Project $project)
    {
        $project->is_completed = true;
        $project->update();

        return response()->json('Project updated!');
    }

    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => ['required','email','unique:users'],
                        'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation',
                        'password_confirmation' => 'required|min:6',
                ],
                [
                        'name.required' => 'Please enter Name.',
                        'email.required' => 'Please enter Email.',
                        'email.email' => 'Please enter valid email address.',
                        'email.unique' => 'Email Already Exists.',
                        'password.required' => 'Please enter Password.',
                        'password.min' => 'The password must be atleast 6 characters.',
                        'password.required_with' => 'Password and Confirm Password does not match2.',
                        'password.same' => 'Password and Confirm Password does not match.',
                        'password_confirmation.required' => 'Please enter Confirm Password.',
                        'password_confirmation.min' => 'The confirm password must be atleast 6 characters.',
                ]
        );

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()],422);
        } 
        else{
            \DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'remember_token' => uniqid().rand(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(['success'=>$request->all()],200);
        }
    }

    public function upload(Request $request)
    {
        // return response()->json(['display'=>$request->file()],422);
    }

    public function weather(Request $request){
        $weather = file_get_contents('http://api.openweathermap.org/data/2.5/weather?lat=' . $request->latitude . '&lon=' . $request->longitude . '&appid=fcd2fe4176dd4e0bf049c3eeb38ae6cf');
        $json = json_decode($weather);
        return response()->json(['display'=>$json->main],200);
    }
    
}
