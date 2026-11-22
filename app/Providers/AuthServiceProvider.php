<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\RepairJob;
use App\Policies\InvoicePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\RepairJobPolicy;
use Illuminate\Support\ServiceProvider;

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
