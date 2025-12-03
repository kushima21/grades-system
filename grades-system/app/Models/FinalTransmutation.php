<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalTransmutation extends Model
{
    use HasFactory;

    // Specify the table name since it's not the plural of the model
    protected $table = 'final_transmutation';

    protected $fillable = [
        'grades',
        'transmutation',
        'remarks',
    ];
}
