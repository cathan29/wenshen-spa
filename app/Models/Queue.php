<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    // This links the model to your "queues" table
    protected $fillable = [
        'queue_number',
        // ❌ 'service_id' has been removed!
        'customer_name',
        'qr_token',
        'status',
        'estimated_serving_time',
        'remarks'
    ];

    // 🌉 THE BRIDGE: A Queue ticket can now have MULTIPLE services
    public function services()
    {
        // 'queue_service' is the name of the pivot table we just made
        return $this->belongsToMany(Service::class, 'queue_service')->withTimestamps();
    }

    // 💰 BONUS HELPER: Instantly calculate the total bill for this ticket
    public function getTotalPriceAttribute()
    {
        return $this->services->sum('price');
    }
}