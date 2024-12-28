<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPaymentPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_payment_plan', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('payment_plan_id');

            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('payment_plan_id')->references('id')->on('payment_plans')->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['account_id', 'payment_plan_id']);
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
        Schema::dropIfExists('account_payment_plan');
    }
}
