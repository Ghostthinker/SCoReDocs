<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->string('avatar')->nullable();
            $table->bigInteger('data_university_id')->unsigned()->nullable();
            $table->foreign('data_university_id')
                ->references('id')
                ->on('data_universities');
            $table->string('course')->nullable();
            $table->integer('matriculation_number')->nullable();
            $table->text('knowledge')->nullable();
            $table->text('personal_resources')->nullable();
            $table->text('about_me')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
