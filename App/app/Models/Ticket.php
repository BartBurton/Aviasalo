<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = "tickets";
    protected $guarded = [];
    public $timestamps = false;

    public function aircompany()
    {
        return $this->belongsTo(Aircompany::class);
    }

    public function segments()
    {
        return $this->hasMany(Segment::class);
    }
}
