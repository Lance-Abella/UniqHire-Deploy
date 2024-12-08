<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'disability_id',
        'educational_id',
        'name',
        'contactnumber',
        'latitude',
        'longitude',
        'location',
        'pwd_id',
        'age',
        'about',
        'founder',
        'year_established',
        'affiliations',
        'awards',
        'paypal_account',
        'registration_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disability()
    {
        return $this->belongsTo(Disability::class, 'disability_id');
    }

    public function education()
    {
        return $this->belongsTo(EducationLevel::class, 'educational_id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'user_id');
    }

    public function skills()
    {
        return $this->hasMany(SkillUser::class, 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany(CertificationDetail::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'sponsor_id');
    }

    public function socials()
    {
        return $this->belongsToMany(Socials::class, 'user_socials', 'user_id', 'social_id');
    }

    public function events()
    {
        return $this->hasMany(Events::class, 'employer_id');
    }
}
