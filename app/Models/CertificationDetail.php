<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    public function skills()
    {
        return $this->belongsToMany(
            Skill::class,                // Related model
            'program_skill',             // Pivot table
            'training_program_id',       // Foreign key on pivot table (related to `TrainingProgram`)
            'skill_id'                   // Foreign key on pivot table (related to `Skill`)
        )->withTimestamps();           // Optionally include timestamps if needed
    }

}
