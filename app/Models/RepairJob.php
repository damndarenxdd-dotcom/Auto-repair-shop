<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class RepairJob extends Model
{
    protected $fillable = [
        'customer_id',
        'mechanic_id',
        'manager_id',
        'vehicle_model',
        'license_plate',
        'year',
        'description',
        'status',
        'estimated_cost',
        'actual_cost',
        'start_date',
        'completion_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'repair_job_services')
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('repair-job.' . $this->id);
    }
}
