<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrowdfundEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'goal'
    ];

    public function program()
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'crowdfund_id');
    }
}
