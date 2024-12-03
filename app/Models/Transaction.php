<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'crowdfund_id',
        'sponsor_id',
        'amount',
        'status',
        'transaction_id'
    ];

    public function crowdfundEvent()
    {
        return $this->belongsTo(CrowdfundEvent::class, 'crowdfund_id');
    }

    // Define the relationship with the sponsor (user info)
    public function sponsor()
    {
        return $this->belongsTo(UserInfo::class, 'sponsor_id');
    }
}
