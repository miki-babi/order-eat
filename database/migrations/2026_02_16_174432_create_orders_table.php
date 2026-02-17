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
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready','delivered','cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('payment_method', ['cash', 'transfer'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('delivery_method', ['self-pickup', 'delivery'])->default('self-pickup');
            $table->timestamp('order_time')->useCurrent();

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
