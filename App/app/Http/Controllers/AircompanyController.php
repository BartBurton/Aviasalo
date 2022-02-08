<?php

namespace App\Http\Controllers;

use App\Models\Aircompany;
use Illuminate\Http\Request;
use App\Exceptions\TokenGenerator;
use App\Models\Segment;

class AircompanyController extends Controller
{
    public function get($id)
    {
        return Aircompany::find($id);
    }

    public function all()
    {
        return Aircompany::all()->sortByDesc('id')->values();
    }

    public function create(Request $request)
    {
        return Aircompany::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $aircomp = Aircompany::find($id);
        $aircomp->update($request->all());
        return $aircomp;
    }

    public function delete($id)
    {
        $aircomp = Aircompany::find($id);

        if (is_file($aircomp->image_path) && file_exists($aircomp->image_path)) {
            unlink(public_path($aircomp->image_path));
        }

        return Aircompany::destroy($id);
    }

    public function loadLogo(Request $request, $id)
    {
        $aircomp = Aircompany::find($id);

        if ($aircomp) {

            if (is_file($aircomp->image_path) && file_exists($aircomp->image_path)) {
                unlink(public_path($aircomp->image_path));
            }

            $upload_path = public_path('aircompanies');
            $generated_new_name = TokenGenerator::generateToken() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move($upload_path, $generated_new_name);

            $aircomp->update([
                'image_path'  => 'aircompanies/' . $generated_new_name
            ]);

            return 'aircompanies/' . $generated_new_name;
        } else {
            return response('not found', 404);
        }
    }
}
