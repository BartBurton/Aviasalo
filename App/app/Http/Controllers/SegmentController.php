<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function get($id)
    {
        return Segment::find($id);
    }
    public function all()
    {
        return Segment::all();
    }
    public function create(Request $request)
    {
        return Segment::create($request->all());
    }
    public function update(Request $request, $id)
    {
        $seg = Segment::find($id);
        $seg->update($request->all());
        return $seg;
    }
    public function delete($id)
    {
        return Segment::destroy($id);
    }
}
