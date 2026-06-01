<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [

        'nama',
        'telepon',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status'

    ];
}