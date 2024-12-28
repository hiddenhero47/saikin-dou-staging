<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcast_templates', function (Blueprint $table) {
            // Identifications
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->uuid('account_id')->index()->nullable();

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
        Schema::dropIfExists('broadcast_templates');
    }
}
