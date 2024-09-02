<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
    ];

    public function skilluser()
    {
        return $this->belongsToMany(SkillUser::class, 'skill_id', 'skill_user', 'user_id');
    }

    public function trainingProgram()
    {
        return $this->hasMany(Skill::class, 'program_skill', 'skill_id', 'program_id');
    }
}
