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
        'service_id',
        'customer_name',
        'qr_token',
        'status',
        'estimated_serving_time'
    ];

    // This allows us to easily get the service details (like price/name) for this queue
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}