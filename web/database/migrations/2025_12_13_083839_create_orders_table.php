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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending',
                'confirmed',
                'assembled',
                'shipped',
                'delivered',
                'paid',
                'cancelled',
            ])->default('pending');
            $table->enum('payment_method', ['cash', 'gcash', 'bank_transfer'])->default('cash');
            $table->decimal('total_amount', 10, 2);
            $table->enum('delivery_method', ['pickup', 'delivery'])->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->string('paymongo_session_id')->nullable();
            $table->string('paymongo_payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
