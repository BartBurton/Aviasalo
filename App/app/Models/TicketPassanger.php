<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPassanger extends Model
{
    use HasFactory;
    protected $table = "tickets_passangers";
    protected $guarded = [];
    public $timestamps = false;
}
