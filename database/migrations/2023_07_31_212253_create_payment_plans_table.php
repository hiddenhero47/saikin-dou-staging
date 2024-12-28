<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            // Identifications
            $table->id();

            // Properties - payment plan
            $table->string('name');
            $table->unsignedSmallInteger('level')->nullable();
            $table->json('payment_plan_benefits')->nullable();
            $table->double('amount', 10, 2)->default(0);
            $table->double('discount', 10, 2)->default(0);
            $table->string('currency', 4)->nullable();
            $table->string('visibility', 24)->default('public');

            // Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_plans');
    }
}
