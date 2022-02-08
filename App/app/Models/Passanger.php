<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Passanger extends Model
{
    use HasFactory;
    protected $table = "passangers";
    protected $guarded = [];
    public $timestamps = false;

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'tickets_passangers')->withPivot('is_booked', 'sale_date')->wherePivot('is_booked', 1);
    }
}
