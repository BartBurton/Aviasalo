<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Resources\TicketResource;
use App\Models\Maker;
use Illuminate\Http\Request;
use App\Exceptions\TokenGenerator;
use App\Models\TicketPassanger;

class TicketController extends Controller
{
    public function get($id)
    {
        return new TicketResource(Ticket::find($id));
    }
    public function all()
    {
        return TicketResource::collection(Ticket::all()->sortByDesc('id')->values());
    }
    public function search($departure, $arrival, $date)
    {
        $tkts = TicketResource::collection(Ticket::all());
        $allowed = [];

        foreach ($tkts as $tkt) {
            $sgmts = $tkt->segments->sortBy('order')->values();

            if (
                $tkt->count > 0
                and $sgmts->first()->departure == $departure
                and $sgmts->last()->arrival == $arrival
                and str_contains($tkt->segments->first()->departure_date, $date)
            ) {
                array_push($allowed, $tkt);
            }
        }

        return $allowed;
    }

    public function passangers($id) {
        return TicketPassanger::all()->where('ticket_id', '=', $id);
    }

    public function cities()
    {
        $tkts = TicketResource::collection(Ticket::all());

        $departures = [];
        $arrivals = [];

        foreach ($tkts as $tkt) {
            if ($tkt->segments->count() > 0) {
                $sgmts = $tkt->segments->sortBy('order')->values();
                array_push($departures, $sgmts->first()->departure);
                array_push($arrivals, $sgmts->last()->arrival);
            }
        }

        $departures = array_values(array_unique($departures));
        $arrivals = array_values(array_unique($arrivals));

        return ([
            'departures' => $departures,
            'arrivals' => $arrivals
        ]);
    }


    public function create(Request $request)
    {
        return Ticket::create($request->all());
    }
    public function update(Request $request, $id)
    {
        $tkt = Ticket::find($id);
        $tkt->update($request->all());
        return $tkt;
    }
    public function delete($id)
    {
        $tkt = Ticket::find($id);

        if (is_file($tkt->preview_img) && file_exists($tkt->preview_img)) {
            unlink(public_path($tkt->preview_img));
        }

        return Ticket::destroy($id);
    }
    public function loadPreview(Request $request, $id)
    {
        $tkt = Ticket::find($id);

        if ($tkt) {

            if (is_file($tkt->preview_img) && file_exists($tkt->preview_img)) {
                unlink(public_path($tkt->preview_img));
            }

            $upload_path = public_path('previews');
            $generated_new_name = TokenGenerator::generateToken() . '.' . $request->file->getClientOriginalExtension();
            $request->file->move($upload_path, $generated_new_name);

            $tkt->update([
                'preview_img'  => 'previews/' . $generated_new_name
            ]);

            return 'previews/' . $generated_new_name;
        } else {
            return response('not found', 404);
        }
    }
}
