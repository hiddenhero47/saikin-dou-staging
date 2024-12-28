<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenefitPaymentPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benefit_payment_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('benefit_id');
            $table->unsignedBigInteger('payment_plan_id');
            $table->unsignedSmallInteger('value');

            $table->foreign('benefit_id')->references('id')->on('benefits')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('payment_plan_id')->references('id')->on('payment_plans')->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['benefit_id', 'payment_plan_id']);
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
        Schema::dropIfExists('benefit_payment_plan');
    }
}
