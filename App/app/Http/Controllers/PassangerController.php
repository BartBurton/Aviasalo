<?php

namespace App\Http\Controllers;

use App\Http\Resources\PassangerResource;
use Illuminate\Http\Request;
use App\Exceptions\TokenGenerator;
use App\Http\Resources\ClientResource;
use App\Http\Resources\TicketResource;
use App\Models\Client;
use App\Models\User;

class PassangerController extends Controller
{
    public function get(Request $request)
    {
        $token = $request->header('Authorization');
        $usr = User::where('remember_token', '=', $token)->first();

        if ($usr) {
            $clt = $usr->clients->first();
            if ($clt) {
                return new ClientResource($clt);
            }
        }

        return response('forbidden', 403);
    }

    public function create(Request $request)
    {
        $clt = Client::where('email', '=', $request->email)->first();

        if (!$clt) {
            $remember_token = TokenGenerator::generateToken();

            $usr = User::create([
                'name'              => $request->name,
                'password'          => $request->password,
                'remember_token'    => $remember_token,
            ]);


            $clt = Client::create([
                'surname'           => $request->surname,
                'dob'               => $request->dob,
                'doc'               => $request->doc,
                'email'             => $request->email,
                'avatar_path'       => 'avatars/default.jpg',
                'user_id'           => $usr->id
            ]);

            return new ClientResource($clt);
        }
        return response('forbidden', 403);
    }

    public function update(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $clt = Client::find($id);


        if ($clt->user->remember_token == $token) {
            $usr = User::find($clt->user->id);
            $usr->update([
                'name'      => $request->name,
                'password'  => $request->password,
            ]);
            
            $clt->update([
                'surname'   => $request->surname,
                'dob'       => $request->dob,
                'doc'       => $request->doc,
                'email'     => $request->email,
            ]);

            return new ClientResource(Client::find($id));
        }
        return response('forbidden', 403);
    }

    public function delete(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $clt = Client::find($id);
        if ($clt->user->remember_token == $token) {
            if ($clt->avatar_path && $clt->avatar_path !== 'avatars/default.jpg') {
                if (file_exists($clt->avatar_path)) {
                    unlink(public_path($clt->avatar_path));
                }
            }
            User::destroy($clt->user->id);
            return response('success', 200);
        }
        return response('forbidden', 403);
    }

    public function tickets(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $usr = User::find(Client::find($id)->user->id);

        if ($usr->remember_token == $token) {
            return TicketResource::collection($usr->tickets);
        } else {
            return response('forbidden', 403);
        }
    }

    public function loadAvatar(Request $request, $id)
    {
        $token = $request->header('Authorization');
        $clt = Client::find($id);
        if ($clt->user->remember_token == $token) {
            if ($clt->avatar_path && $clt->avatar_path !== 'avatars/default.jpg') {
                if (file_exists($clt->avatar_path)) {
                    unlink(public_path($clt->avatar_path));
                }
            }

            $upload_path = public_path('avatars');
            $generated_new_name = TokenGenerator::generateToken() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move($upload_path, $generated_new_name);

            $clt->update([
                'avatar_path'  => 'avatars/' . $generated_new_name
            ]);

            return 'avatars/' . $generated_new_name;
        }
        return response('forbidden', 403);
    }
}
