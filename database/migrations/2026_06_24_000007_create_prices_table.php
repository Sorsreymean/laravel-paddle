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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('paddle_id')->unique();
            $table->string('paddle_product_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('tax_mode')->nullable();
            $table->decimal('amount',18,4)->nullable();
            $table->string('currency',50)->nullable();
            $table->string('interval',100)->nullable();
            $table->string('frequency',50)->nullable();
            $table->json('unit_price')->nullable();
            $table->json('billing_cycle')->nullable();
            $table->json('trial_period')->nullable();
            $table->json('quantity')->nullable();
            $table->json('custom_data')->nullable();
            $table->timestamp('paddle_created_at')->nullable();
            $table->timestamp('paddle_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
