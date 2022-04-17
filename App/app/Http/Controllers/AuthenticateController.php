<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employer;
use App\Models\Role;
use App\Models\Client;

class AuthenticateController extends Controller
{
    public function isUser(Request $request)
    {
        $token = $request->token;
        $clt = User::where('remember_token', '=', $token)->first()->clients->first();

        return !!$clt;
    }

    public function isMaker(Request $request)
    {
        $token = $request->token;
        $emp = User::where('remember_token', '=', $token)->first()->employers->first();
        
        return !!$emp && $emp->is_active;
    }

    public function isAdmin(Request $request)
    {
        $token = $request->token;
        $emp = User::where('remember_token', '=', $token)->first()->employers->first();
        return !!$emp && $emp->is_active && $emp->role->name == 'admin';
    }
}
