<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;
    protected $fillable = [
        'employer_id',
        'position',
        'description',
        'salary',
        'end_date',
        'latitude',
        'longitude',
        'location',
        'start_age',
        'end_age'
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function disability()
    {
        return $this->belongsToMany(Disability::class, 'job_disability', 'job_listing_id', 'disability_id');
    }

    public function skill()
    {
        return $this->belongsToMany(Skill::class, 'job_skill', 'job_listing_id', 'skill_id');
    }

    public function education()
    {
        return $this->belongsTo(EducationLevel::class, 'education_id');
    }
}
