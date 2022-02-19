<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maker;
use App\Exceptions\TokenGenerator;
use App\Models\Passanger;
use App\Http\Resources\PassangerResource;

class AdminController extends Controller
{
    public function get(Request $request)
    {
        $token = $request->header('Authorization');
        $mkr = Maker::where('remember_token', '=', $token)->first();

        if ($mkr) {
            return $mkr;
        } else {
            return response('forbiden', 403);
        }
    }

    public function allMakers()
    {
        return Maker::all()->sortByDesc('id')->values();
    }

    public function getMaker($id)
    {
        return Maker::find($id);
    }

    public function createMaker(Request $request)
    {
        $remember_token = TokenGenerator::generateToken();

        return Maker::create([
            'name'              => $request->name,
            'password'          => $request->password,
            'role'              => $request->role,
            'is_active'         => false,
            'remember_token'    => $remember_token,
        ]);
    }

    public function updateMaker(Request $request, $id)
    {
        $mkr = Maker::find($id);
        $mkr->update([
            'name'              => $request->name,
            'password'          => $request->password,
            'role'              => $request->role,
            'is_active'         => $request->is_active,
        ]);

        return response('success', 200);
    }

    public function deleteMaker($id)
    {
        Maker::destroy($id);

        return response('success', 200);
    }



    public function allPassangers()
    {
        return PassangerResource::collection(Passanger::all());
    }

    public function getPassanger(Request $request, $id)
    {
        return new PassangerResource(Passanger::find($id));
    }

    public function updatePassanger(Request $request, $id)
    {
        $pass = Passanger::find($id);
        $pass->update($request->all());

        return response('success', 200);
    }

    public function deletePassanger($id)
    {
        Passanger::destroy($id);

        return response('success', 200);
    }
}
