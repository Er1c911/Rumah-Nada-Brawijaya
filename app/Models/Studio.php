<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable = [
    'nama_band',
    'tanggal',
    'jam',
    'durasi'
];
}
