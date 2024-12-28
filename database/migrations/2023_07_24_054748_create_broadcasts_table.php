<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            // Identifications
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('account_id')->index();

            // Properties - broadcast
            $table->string('title', 50)->nullable();
            $table->longText('message')->nullable();
            $table->mediumText('pictures')->nullable();
            $table->mediumText('videos')->nullable();
            $table->string('preview_phone', 25)->nullable();
            $table->dateTime('contact_group_start_date',0)->nullable();
            $table->dateTime('contact_group_end_date',0)->nullable();
            $table->unsignedBigInteger('contact_group_id')->nullable();
            $table->json('whatsapp_group_names')->nullable();

            // Status
            $table->string('status')->default('pending')->comment('pending, queued, delivered, canceled');
            $table->unsignedSmallInteger('total_outgoing')->default(0);
            $table->unsignedSmallInteger('successful_outgoing')->default(0);
            $table->unsignedSmallInteger('failed_outgoing')->default(0);
            $table->unsignedSmallInteger('messages_before_pause')->default(10);
            $table->time('minutes_before_resume',0)->default('00:03:00');
            $table->unsignedSmallInteger('flag')->default(0);
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
        Schema::dropIfExists('broadcasts');
    }
}
