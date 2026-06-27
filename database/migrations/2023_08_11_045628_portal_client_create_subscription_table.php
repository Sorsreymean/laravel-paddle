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
        if(!Schema::hasTable('subscription')){
            Schema::create('subscription', function (Blueprint $table) {
                $table->id();
                $table->string('subscription_name');
                $table->string('sub_id');
                $table->string('email')->nullable();
                $table->integer('plan_id');
                $table->text('description')->nullable();
                $table->tinyInteger('status');
                $table->string('company_file');
                $table->dateTime('activated_date')->nullable();
                $table->dateTime('expired_date')->nullable();
                $table->string('paddle_subscription_id')->nullable();
                $table->string('updated_by');
                $table->string('created_by');
                $table->softDeletes($column = 'deleted_at', $precision = 0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription');
    }
};