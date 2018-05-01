<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->tinyInteger('gender')->default(0);
            $table->date('birth_date')->nullable();
            $table->integer('categories')->default(0);
            $table->string('about')->nullable();
            $table->string('experience')->nullable();
            $table->integer('available_days')->default(0);
            $table->integer('languages')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
