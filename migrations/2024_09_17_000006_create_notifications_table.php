<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('repair_job_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['job_assigned', 'job_status_change', 'job_completed', 'payment_due', 'new_message'])->default('job_status_change');
            $table->boolean('read')->default(false);
            $table->datetime('read_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('repair_job_id')->references('id')->on('repair_jobs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
