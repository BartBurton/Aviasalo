<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Exceptions\TokenGenerator;
use App\Http\Resources\TicketResource;
use App\Models\BookingTransaction;
use App\Models\Client;
use GuzzleHttp\RetryMiddleware;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $tkt = new TicketResource(Ticket::find($request->ticket_id));
        if (!$tkt or $tkt->segments->count() <= 0) {
            return response('not found', 404);
        }
        $tkt_for_book = [
            'ticket_id'         => $tkt->id,
            'departure'         => $tkt->segments->sortBy('order')->values()->first()->departure,
            'arrival'           => $tkt->segments->sortBy('order')->values()->last()->arrival,
            'departure_date'    => $tkt->segments->sortBy('order')->values()->first()->departure_date,
            'arrival_date'      => $tkt->segments->sortBy('order')->values()->last()->arrival_date,
            'aircompany'        => $tkt->aircompany->name,
            'cost'              => $tkt->price,
        ];


        $pass = $this->createPassangerForBooking($request);



        if(BookingTransaction
            ::where('ticket_id', '=', $tkt->id)
            ->where('doc', '=', $pass['doc'])
            ->first()){
            return response('forbiden', 403);
        }   



        $booking_token = TokenGenerator::generateToken();

        return BookingTransaction::create([
            'ticket_id'         => $tkt_for_book['ticket_id'],
            'departure'         => $tkt_for_book['departure'],
            'arrival'           => $tkt_for_book['arrival'],
            'departure_date'    => $tkt_for_book['departure_date'],
            'arrival_date'      => $tkt_for_book['arrival_date'],
            'aircompany'        => $tkt_for_book['aircompany'],
            'cost'              => $tkt_for_book['cost'],

            'user_id'       => $pass['id'],
            'name'          => $pass['name'],
            'surname'       => $pass['surname'],
            'dob'           => $pass['dob'],
            'doc'           => $pass['doc'],
            'email'         => $pass['email'],

            'is_closed'     => false,
            'transaction_token' => $booking_token,

            'transaction_date'     => date('c')
        ]);
    }

    public function confirm(Request $request)
    {
        $tkt = Ticket::find($request->ticket_id);
        if (!$tkt) {
            return response('not found', 404);
        }
        if (!BookingTransaction::where('email', '=', $request->email)->first()) {
            return response('not found', 404);
        }

        $transaction = BookingTransaction::where('transaction_token', '=', $request->booking_token)->first();

        if ($transaction) {
            $tkt->update(['count' => $tkt->count - 1]);
            $transaction->update(['is_closed' => true]);
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
            'id'       => $request->passanger['id'] ?? null
        ];

        $clt = Client::where('email', '=', $request->passanger['email'])->first();
        if($clt) {
            $result['id'] = $clt->user->id;
        }
        return $result;
    }
}
