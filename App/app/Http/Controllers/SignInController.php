<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Client;
class SignInController extends Controller
{
    public function signInUser($email, $password)
    {
        $clt = Client::where('email', '=', $email)->first();
        if($clt && $clt->user->password == $password)
        {
            return $clt->user->remember_token;
        }
        return response('forbidden', 403);
    }

    public function signInMaker($name, $password)
    {
        $usr = User::where('password', '=', $password)->first();
        if($usr)
        {
            $emp = $usr->employers->first();

            if($emp && $emp->is_active && $emp->user->name == $name)
            {
                return $usr->remember_token;
            }
        }
        return response('forbidden', 403);
    }

    public function signInAdmin($name, $password)
    {
        $usr = User::where('password', '=', $password)->first();
        if($usr)
        {
            $emp = $usr->employers->first();

            if($emp && $emp->is_active && $emp->role->name == 'admin' && $emp->user->name == $name)
            {
                return $usr->remember_token;
            }
        }
        return response('forbidden', 403);
    }
}
