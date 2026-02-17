<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name', 
        'price', 
        'is_active'
    ];

    // 🌉 THE REVERSE BRIDGE: A Service can belong to MULTIPLE queue tickets
    public function queues()
    {
        return $this->belongsToMany(Queue::class, 'queue_service')->withTimestamps();
    }
}