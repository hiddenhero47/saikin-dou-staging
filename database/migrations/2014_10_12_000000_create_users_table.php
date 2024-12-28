<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Identifications
            $table->uuid('id')->primary();
            $table->string('email', 100)->unique();

            // Properties - user
            $table->string('name', 50)->unique();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->rememberToken();
            $table->date('birth_date')->nullable();
            $table->year('birth_year')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('picture', 255)->nullable();
            $table->string('gender',1)->default(9)->comment('1 is male, 2 is female, 9 is unknown');
            $table->mediumText('description')->nullable();
            $table->json('providers_allowed')->nullable();
            $table->json('providers_disallowed')->nullable();
            $table->json('providers_details')->nullable();
            $table->json('groups')->nullable();
            $table->string('referrer_code', 50)->nullable();

            // Status
            $table->boolean('user_details_verified')->default(false);
            $table->boolean('blocked')->default(false);
            $table->unsignedSmallInteger('flag')->default(0);
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
        Schema::dropIfExists('users');
    }
}
