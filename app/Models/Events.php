<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'schedule',
        'start_time',
        'end_time',
        'employer_id'
    ];

    public function employer()
    {
        return $this->belongsTo(UserInfo::class, 'employer_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'participants', 'event_id', 'user_id');
    }
}
