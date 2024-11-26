<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'program_id',
        'user_id',
    ];

    public function program()
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    public function user()
    {
        return $this->belongsTo(UserInfo::class, 'user_id');
    }
}
