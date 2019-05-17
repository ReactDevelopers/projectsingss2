<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Update the user's contact information settings.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $user = User::updateNotificationOptions($request, Auth::user()->id);

        return response()->json($user);
    }
}
