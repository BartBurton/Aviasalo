<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircompany extends Model
{
    use HasFactory;
    
    protected $table = "aircompanies";
    protected $guarded = [];
    public $timestamps = false;
}
