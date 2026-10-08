<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        \DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'manager', 'description' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mechanic', 'description' => 'Mechanic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'customer', 'description' => 'Customer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
