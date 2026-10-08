<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price', 'estimated_hours', 'is_active'];

    public function repairJobs()
    {
        return $this->belongsToMany(RepairJob::class, 'repair_job_services')
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();
    }
}
