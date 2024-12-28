<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmbeddedFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('embedded_forms', function (Blueprint $table) {
            // Identification
            $table->id();
            $table->uuid('user_id')->index();
            $table->unsignedBigInteger('group_id')->index();
            $table->string('form_url',100)->unique()->index();

            // Properties - embedded form
            $table->string('title',100);
            $table->string('custom_url',100)->nullable();
            $table->mediumText('description')->nullable();
            $table->json('input_fields')->nullable();
            $table->string('form_header_text', 255)->nullable();
            $table->mediumText('form_header_images', 255)->nullable();
            $table->string('form_footer_text', 255)->nullable();
            $table->mediumText('form_footer_images', 255)->nullable();
            $table->string('form_background_color',20)->nullable();
            $table->string('form_width',30)->nullable()->comment('small,normal,large');
            $table->string('form_border_radius',10)->nullable();
            $table->string('submit_button_color',20)->nullable();
            $table->string('submit_button_text',100)->nullable();
            $table->string('submit_button_text_color',20)->nullable();
            $table->string('submit_button_text_before',100)->nullable();
            $table->string('submit_button_text_after',100)->nullable();
            $table->string('thank_you_message',255)->nullable();
            $table->string('thank_you_message_url',255)->nullable();
            $table->string('facebook_pixel_code',100)->nullable();
            $table->unsignedTinyInteger('auto_responder_id')->nullable();

            // Status
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('embedded_forms');
    }
}
