<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageMentionings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_mentionings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->bigInteger('message_id')->unsigned()->nullable();
            $table->foreign('message_id')
                ->references('id')
                ->on('messages');
            $table->bigInteger('project_id')->unsigned()->nullable();
            $table->foreign('project_id')
                ->references('id')
                ->on('projects');
            $table->timestamp('read_at')->nullable();
            $table->unique(['user_id', 'message_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_mentionings');
    }
}
