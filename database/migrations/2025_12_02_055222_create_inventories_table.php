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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_code', 20)->unique();
            $table->string('brand_name', 100)->nullable();
            $table->string('item_name', 100);
            $table->text('description')->nullable();
            $table->enum('category', [
                'Hand Tools',
                'Power Tools',
                'Construction Materials',
                'Locks and Security',
                'Plumbing',
                'Electrical',
                'Paint and Finishes',
                'Chemicals'
            ]);
            $table->string('item_image_1', 255)->nullable();
            $table->string('item_image_2', 255)->nullable();
            $table->string('item_image_3', 255)->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->integer('restock_threshold')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
