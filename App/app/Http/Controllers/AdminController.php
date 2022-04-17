<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employer;
use App\Models\Role;
use App\Models\Client;
use App\Exceptions\TokenGenerator;
use App\Http\Resources\ClientResource;
use App\Http\Resources\EmployerResource;

class AdminController extends Controller
{
    public function get(Request $request)
    {
        $token = $request->header('Authorization');
        $usr = User::where('remember_token', '=', $token)->first();

        if ($usr) {
            $emp = $usr->employers->first();
            if ($emp) {
                return new EmployerResource($emp);
            }
        }
        return response('forbidden', 403);
    }

    public function allMakers()
    {
        return EmployerResource::collection(Employer::all()->sortByDesc('id')->values());
    }

    public function getMaker($id)
    {
        $emp = Employer::find($id);
        return new EmployerResource($emp);
    }

    public function createMaker(Request $request)
    {
        $remember_token = TokenGenerator::generateToken();

        $usr = User::create([
            'name'              => $request->name,
            'password'          => $request->password,
            'remember_token'    => $remember_token,
        ]);

        $role_name = $request->role;
        $role = Role::where('name', '=', $role_name)->first();

        $emp = Employer::create([
            'is_active'     => $request->is_active,
            'role_id'       => $role->id,
            'user_id'       => $usr->id
        ]);

        return new EmployerResource($emp);
    }

    public function updateMaker(Request $request, $id)
    {
        $emp = Employer::find($id);

        $role_name = $request->role;
        $role = Role::where('name', '=', $role_name)->first();

        $usr = User::find($emp->user->id);

        $usr->update([
            'name'      => $request->name,
            'password'  => $request->password,
        ]);

        $emp->update([
            'is_active'     => $request->is_active,
            'role_id'       => $role->id,
        ]);

        return new EmployerResource(Employer::find($id));
    }

    public function deleteMaker($id)
    {
        $emp = Employer::find($id);

        User::destroy($emp->user->id);

        return response('success', 200);
    }



    public function allPassangers()
    {
        return ClientResource::collection(Client::all());
    }

    public function getPassanger($id)
    {
        return new ClientResource(Client::find($id));
    }

    public function updatePassanger(Request $request, $id) {
        $clt = Client::find($id);

        if ($clt) {
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

    public function deletePassanger($id)
    {
        $clt = Client::find($id);
        if ($clt) {
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
}
