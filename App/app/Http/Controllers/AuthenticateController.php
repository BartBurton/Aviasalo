<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Passanger;
use App\Models\Maker;

class AuthenticateController extends Controller
{
    public function isUser(Request $request)
    {
        $token = $request->token;
        $pass = Passanger::where('remember_token', '=', $token)->first();

        return !!$pass && !!$pass->password;
    }

    public function isMaker(Request $request)
    {
        $token = $request->token;
        $pass = Maker::where('remember_token', '=', $token)->first();

        return !!$pass && $pass->is_active;
    }

    public function isAdmin(Request $request)
    {
        $token = $request->token;
        $pass = Maker::where('remember_token', '=', $token)->first();

        return !!$pass && $pass->role == 'admin' && $pass->is_active;
    }
}
