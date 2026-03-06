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
        Schema::create('ir_alerts', function (Blueprint $table) {
            $table->id();
            // item_code if RFID was scanned just before the alert, null if unscanned
            $table->string('item_code')->nullable();
            // 'unscanned' = IR triggered but no RFID scan, 'scanned' = item was scanned
            $table->enum('alert_type', ['unscanned', 'scanned'])->default('unscanned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ir_alerts');
    }
};
