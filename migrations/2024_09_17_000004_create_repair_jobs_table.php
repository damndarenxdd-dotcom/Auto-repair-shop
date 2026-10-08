<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('mechanic_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->string('vehicle_model');
            $table->string('license_plate');
            $table->year('year')->nullable();
            $table->text('description');
            $table->enum('status', ['pending', 'assigned', 'in-progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mechanic_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_jobs');
    }
};
