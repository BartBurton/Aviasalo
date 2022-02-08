<?php

namespace App\Http\Controllers;

use App\Models\Maker;

class SignInController extends Controller
{
    public function signIn($name, $password)
    {
        return Maker
            ::where('name',     '=', $name)
            ->where('password', '=', $password)
            ->first()
            ->remember_token;
    }
}
