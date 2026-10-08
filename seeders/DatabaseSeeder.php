<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 1, // admin
            'phone' => '555-0001',
            'address' => '123 Main St, City, State 12345',
        ]);

        // Create manager users
        User::create([
            'name' => 'John Manager',
            'email' => 'manager@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 2, // manager
            'phone' => '555-0002',
            'address' => '456 Oak Ave, City, State 12345',
        ]);

        // Create mechanic users
        User::create([
            'name' => 'Mike Mechanic',
            'email' => 'mechanic1@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 3, // mechanic
            'phone' => '555-0003',
            'address' => '789 Elm St, City, State 12345',
        ]);

        User::create([
            'name' => 'Tom Technician',
            'email' => 'mechanic2@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 3, // mechanic
            'phone' => '555-0004',
            'address' => '321 Pine Rd, City, State 12345',
        ]);

        // Create customer users
        User::create([
            'name' => 'Alice Johnson',
            'email' => 'customer1@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 4, // customer
            'phone' => '555-1001',
            'address' => '111 Maple Dr, City, State 12345',
        ]);

        User::create([
            'name' => 'Bob Smith',
            'email' => 'customer2@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 4, // customer
            'phone' => '555-1002',
            'address' => '222 Birch Ln, City, State 12345',
        ]);

        User::create([
            'name' => 'Carol Williams',
            'email' => 'customer3@autorepairshop.test',
            'password' => Hash::make('password'),
            'role_id' => 4, // customer
            'phone' => '555-1003',
            'address' => '333 Cedar Way, City, State 12345',
        ]);

        // Create services
        $services = [
            ['name' => 'Oil Change', 'description' => 'Regular oil and filter change', 'price' => 45.00, 'estimated_hours' => 0.5],
            ['name' => 'Tire Rotation', 'description' => 'Rotate and balance all tires', 'price' => 50.00, 'estimated_hours' => 0.75],
            ['name' => 'Brake Service', 'description' => 'Brake pad replacement and rotor resurfacing', 'price' => 150.00, 'estimated_hours' => 2],
            ['name' => 'Battery Replacement', 'description' => 'Replace car battery', 'price' => 120.00, 'estimated_hours' => 0.5],
            ['name' => 'Air Filter Change', 'description' => 'Replace engine air filter', 'price' => 30.00, 'estimated_hours' => 0.25],
            ['name' => 'Transmission Service', 'description' => 'Full transmission fluid and filter change', 'price' => 200.00, 'estimated_hours' => 1.5],
            ['name' => 'Suspension Repair', 'description' => 'Shock absorber and strut replacement', 'price' => 300.00, 'estimated_hours' => 3],
            ['name' => 'Engine Diagnostics', 'description' => 'Computer diagnostic scan and analysis', 'price' => 75.00, 'estimated_hours' => 1],
            ['name' => 'A/C Service', 'description' => 'Air conditioning refrigerant recharge', 'price' => 100.00, 'estimated_hours' => 1],
            ['name' => 'Coolant Flush', 'description' => 'Cooling system flush and refill', 'price' => 85.00, 'estimated_hours' => 1],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['is_active' => true]));
        }
    }
}
