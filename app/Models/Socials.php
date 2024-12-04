<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socials extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function userSocial()
    {
        return $this->belongsToMany(UserInfo::class, 'user_socials', 'social_id', 'user_id');
    }
}
