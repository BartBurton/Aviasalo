<?php

namespace App\Http\Controllers;

use App\Models\Passanger;
use App\Http\Resources\PassangerResource;
use Illuminate\Http\Request;
use App\Exceptions\TokenGenerator;
use App\Http\Resources\TicketResource;

class PassangerController extends Controller
{
    public function get(Request $request)
    {
        $token = $request->header('Authorization');
        $pass = Passanger::where('remember_token', '=', $token)->first();

        if ($pass) {
            return new PassangerResource($pass);
        } else {
            return response('forbiden', 403);
        }
    }

    public function signIn($email, $password)
    {
        return Passanger
            ::where('email',    '=', $email)
            ->where('password', '=', $password)
            ->first()
            ->remember_token;
    }

    public function create(Request $request)
    {
        $remember_token = TokenGenerator::generateToken();
        $pass = Passanger::where('email', '=', $request->email)->first();

        if (!$pass) {
            return Passanger::create([
                'name'              => $request->name,
                'surname'           => $request->surname,
                'dob'               => $request->dob,
                'doc'               => $request->doc,
                'email'             => $request->email,
                'password'          => $request->password,
                'remember_token'    => $remember_token,
                'avatar_path'       => 'avatars/default.jpg'
            ]);
        } else {
            return response('forbiden', 403);
        }
    }

    public function update(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $pass = Passanger::find($id);

        if ($pass->remember_token == $token) {
            $pass->update([
                'name'      => $request->name,
                'surname'   => $request->surname,
                'dob'       => $request->dob,
                'doc'       => $request->doc,
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            return response('success', 200);
        } else {
            return response('forbiden', 403);
        }
    }

    public function delete(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $pass = Passanger::find($id);

        if ($pass->remember_token == $token) {
            if ($pass->avatar_path && $pass->avatar_path !== 'avatars/default.jpg') {
                if (file_exists($pass->avatar_path)) {
                    unlink(public_path($pass->avatar_path));
                }
            }

            Passanger::destroy($id);

            return response('success', 200);
        } else {
            return response('forbiden', 403);
        }
    }

    public function tickets(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $pass = Passanger::find($id);

        if ($pass->remember_token == $token) {
            return TicketResource::collection($pass->tickets);
        } else {
            return response('forbiden', 403);
        }
    }

    public function loadAvatar(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $pass = Passanger::find($id);

        if ($pass->remember_token == $token) {

            if ($pass->avatar_path && $pass->avatar_path !== 'avatars/default.jpg') {
                if (file_exists($pass->avatar_path)) {
                    unlink(public_path($pass->avatar_path));
                }
            }

            $upload_path = public_path('avatars');
            $generated_new_name = TokenGenerator::generateToken() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move($upload_path, $generated_new_name);

            $pass->update([
                'avatar_path'  => 'avatars/' . $generated_new_name
            ]);

            return 'avatars/' . $generated_new_name;
        } else {
            return response('forbiden', 403);
        }
    }
}
