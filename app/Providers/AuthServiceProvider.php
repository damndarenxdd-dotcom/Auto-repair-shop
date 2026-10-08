<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Gate;
use App\Models\RepairJob;
use App\Models\Notification;
use App\Models\Invoice;
use App\Policies\RepairJobPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\InvoicePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RepairJob::class => RepairJobPolicy::class,
        Notification::class => NotificationPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
