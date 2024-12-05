<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'pwd_id',
        'job_id',
        'job_application_id',
        'hiring_status',
        'schedule',
        'start_time',
        'end_time'
    ];

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function job()
    {
        return $this->belongsTo(JobListing::class, 'job_id');
    }

    public function pwd()
    {
        return $this->belongsTo(User::class, 'pwd_id');
    }
}
