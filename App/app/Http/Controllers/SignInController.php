<?php

namespace App\Http\Controllers;

use App\Models\Maker;
use App\Models\Passanger;

class SignInController extends Controller
{
    public function signInUser($email, $password)
    {
        return Passanger
            ::where('email',    '=', $email)
            ->where('password', '=', $password)
            ->first()
            ->remember_token;
    }

    public function signInMaker($name, $password)
    {
        return Maker
            ::where('name',     '=', $name)
            ->where('password', '=', $password)
            ->where('is_active', '=', true)
            ->first()
            ->remember_token;
    }

    public function signInAdmin($name, $password)
    {
        return Maker
            ::where('name',     '=', $name)
            ->where('password', '=', $password)
            ->where('role',     '=', 'admin')
            ->where('is_active', '=', true)
            ->first()
            ->remember_token;
    }
}
