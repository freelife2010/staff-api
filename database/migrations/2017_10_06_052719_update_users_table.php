<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_no')->nullable();
            $table->boolean('type');
            $table->string('avatar_url')->nullable();
            $table->double('latitude');
            $table->double('longitude');
            $table->tinyInteger('purchase_level')->default(0);
            $table->dateTime('purchase_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
