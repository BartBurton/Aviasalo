<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Passanger;
use App\Models\Ticket;
use App\Models\TicketPassanger;
use App\Exceptions\TokenGenerator;
use App\Http\Resources\TicketResource;
use GuzzleHttp\RetryMiddleware;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $tkt = new TicketResource(Ticket::find($request->ticket_id));
        if (!$tkt or $tkt->segments->count() <= 0) {
            return response('not found', 404);
        }

        $pass = $this->createPassangerForBooking($request);

        if(TicketPassanger
            ::where('ticket_id', '=', $request->ticket_id)
            ->where('doc', '=', $request->passanger['doc'])
            ->first()){
            return response('forbiden', 403);
        }   

        $booking_token = TokenGenerator::generateToken();
        $tktID = $request->ticket_id;

        return TicketPassanger::create([
            'ticket_id'     => $tktID,
            'passanger_id'  => $pass['id'],
            'name'          => $pass['name'],
            'surname'       => $pass['surname'],
            'dob'           => $pass['dob'],
            'doc'           => $pass['doc'],
            'email'         => $pass['email'],
            'is_booked'     => false,
            'booking_token' => $booking_token,
            'sale_date'     => date('c')
        ]);
    }

    public function confirm(Request $request)
    {
        $tkt = Ticket::find($request->ticket_id);
        if (!$tkt) {
            return response('not found', 404);
        }
        if (!TicketPassanger::where('email', '=', $request->email)->first()) {
            return response('not found', 404);
        }

        $transaction = TicketPassanger::where('booking_token', '=', $request->booking_token)->first();

        if ($transaction) {
            $tkt->update(['count' => $tkt->count - 1]);
            $transaction->update(['is_booked' => true]);
        } else {
            return response('not found', 404);
        }

        return response('success', 200);
    }

    private function createPassangerForBooking($request)
    {
        $result = [
            'name'     => $request->passanger['name'],
            'surname'  => $request->passanger['surname'],
            'dob'      => $request->passanger['dob'],
            'doc'      => $request->passanger['doc'],
            'email'    => $request->passanger['email'],
            'id'       => null
        ];

        $pass = Passanger::where('email', '=', $request->passanger['email'])->first();
        if($pass) {
            $result['id'] = $pass->id;
        } 

        return $result;
    }
}
