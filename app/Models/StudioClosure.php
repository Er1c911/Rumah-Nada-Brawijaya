<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioClosure extends Model
{
    protected $table = 'studio_closures';

    protected $fillable = [
        'tanggal',
        'alasan'
    ];
}