<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastOutgoingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcast_outgoings', function (Blueprint $table) {
            // Identifications
            $table->id();
            $table->uuid('user_id')->index();
            $table->uuid('account_id')->index();
            $table->uuid('broadcast_id')->index();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('whatsapp_group_name',100)->nullable();
            $table->uuid('reference')->index()->nullable();

            // Properties - broadcast outgoings
            $table->string('batch', 100)->nullable();
            $table->longText('exception')->nullable();

            // Status
            $table->string('status')->default('pending')->comment('pending, queued, delivered, canceled');
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
        Schema::dropIfExists('broadcast_outgoings');
    }
}
