<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $customer_id
 * @property int|null $mechanic_id
 * @property int|null $manager_id
 * @property string $vehicle_model
 * @property string $license_plate
 * @property int|null $year
 * @property string $description
 * @property string $status
 * @property numeric|null $estimated_cost
 * @property numeric|null $actual_cost
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $completion_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $customer
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\User|null $manager
 * @property-read \App\Models\User|null $mechanic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereActualCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereCompletionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereLicensePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereMechanicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereVehicleModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairJob whereYear($value)
 * @mixin \Eloquent
 */
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
        'notes',
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
        return new PrivateChannel('repair-job.'.$this->id);
    }
}
