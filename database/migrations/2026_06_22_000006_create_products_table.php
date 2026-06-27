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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('paddle_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('tax_category')->nullable();
            $table->string('image_url')->nullable();
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
        Schema::dropIfExists('products');
    }
};
