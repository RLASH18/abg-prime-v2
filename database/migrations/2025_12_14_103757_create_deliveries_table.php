<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['scheduled', 'rescheduled', 'in_transit', 'delivered', 'failed'])->default('scheduled');
            $table->date('scheduled_date');
            $table->date('actual_delivery_date')->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->string('proof_of_delivery')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
