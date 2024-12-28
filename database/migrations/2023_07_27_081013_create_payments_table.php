<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            // Identification
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('pfm')->unique()->index();
            $table->unsignedBigInteger('account_id')->nullable();

            // Properties - payment
            $table->string('type', 50)->default('standard')->comment('standard, collect');
            $table->string('currency', 4)->nullable();
            $table->double('amount', 10, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->string('method', 50)->nullable();
            $table->json('details')->nullable();
            $table->string('reference',100)->nullable();

            // Status
            $table->string('status')->default('pending')->comment('pending, success, failure');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('payments');
    }
}
