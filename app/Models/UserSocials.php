<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocials extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'social_id',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(UserInfo::class, 'user_id');
    }

    public function social()
    {
        return $this->belongsTo(Socials::class, 'social_id');
    }
}
