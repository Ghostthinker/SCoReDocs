<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaybackCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playback_commands', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('duration');
            $table->bigInteger('timestamp');
            $table->string('title')->nullable();
            $table->string('type');
            $table->json('additional_fields');
            $table->bigInteger('video_nid')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('video_nid')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playback_commands');
    }
}
