<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTransaction extends Model
{
    use HasFactory;

    protected $table = "booking_transaction";
    protected $guarded = [];
    public $timestamps = false;
}
