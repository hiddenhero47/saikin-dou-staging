<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrowsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('browsers', function (Blueprint $table) {
            // Identifications
            $table->id();
            $table->uuid('user_id')->index();
            $table->uuid('account_id')->index();

            // Properties - broadcast
            $table->string('session_id')->nullable();
            $table->longText('browser_instance')->nullable();

            // Status
            $table->string('status')->default('open')->comment('open, idle, closed');
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
        Schema::dropIfExists('browsers');
    }
}
