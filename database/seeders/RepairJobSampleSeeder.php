<?php

namespace Database\Seeders;

use App\Models\RepairJob;
use App\Models\User;
use Illuminate\Database\Seeder;

class RepairJobSampleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::whereHas('role', fn ($query) => $query->where('name', 'customer'))->pluck('id')->toArray();
        $mechanics = User::whereHas('role', fn ($query) => $query->where('name', 'mechanic'))->pluck('id')->toArray();
        $managers = User::whereHas('role', fn ($query) => $query->where('name', 'manager'))->pluck('id')->toArray();

        $vehicles = [
            'Toyota Corolla',
            'Honda Civic',
            'Ford F-150',
            'BMW 3 Series',
            'Mercedes C-Class',
            'Nissan Altima',
            'Chevrolet Malibu',
            'Hyundai Elantra',
            'Volkswagen Jetta',
            'Mazda 3',
        ];

        $statuses = ['pending', 'assigned', 'in-progress', 'completed', 'cancelled'];

        foreach (range(1, 20) as $index) {
            $customerId = $customers[$index % count($customers)];
            $mechanicId = $mechanics[$index % count($mechanics)];
            $managerId = $managers[$index % count($managers)];
            $status = $statuses[$index % count($statuses)];

            $startDate = now()->subDays(30 - ($index % 20))->subHours($index * 2);
            $completionDate = match ($status) {
                'completed' => $startDate->copy()->addHours(3 + ($index % 5)),
                'cancelled' => $startDate->copy()->addHours(1),
                default => null,
            };

            RepairJob::create([
                'customer_id' => $customerId,
                'mechanic_id' => $mechanicId,
                'manager_id' => $managerId,
                'vehicle_model' => $vehicles[$index % count($vehicles)],
                'license_plate' => sprintf('ABC-%02d', $index),
                'year' => 2018 + ($index % 8),
                'description' => "Sample repair job {$index} for maintenance and diagnostics.",
                'status' => $status,
                'estimated_cost' => 120 + ($index * 15),
                'actual_cost' => $status === 'completed' ? 140 + ($index * 17) : null,
                'start_date' => $startDate,
                'completion_date' => $completionDate,
                'notes' => 'Generated sample job order for testing dashboard and workflows.',
            ]);
        }
    }
}
