<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $guarded = [];
    public $timestamps = false;

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function employers()
    {
        return $this->hasMany(Employer::class);
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'booking_transaction')->withPivot('is_closed', 'transaction_date')->wherePivot('is_closed', 1);
    }
}
