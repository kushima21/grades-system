<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalRemark extends Model
{
    use HasFactory;

    // Explicit table name
    protected $table = 'final_remarks';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'grades',
        'transmutation',
        'remarks',
    ];

    // Casts for proper data types
    protected $casts = [
        'grades' => 'decimal:2',
        'transmutation' => 'decimal:2',
    ];
}
