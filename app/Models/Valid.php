<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valid extends Model
{
    use HasFactory;

    protected $fillable = [
        'valid_id_number',        
    ];
}
