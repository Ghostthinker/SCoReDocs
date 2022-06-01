<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->text('drawing_data')->nullable();
            $table->string('rating')->nullable();
            $table->bigInteger('timestamp');
            $table->bigInteger('video_nid')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('annotations')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('video_nid')
                ->references('id')
                ->on('media')
                ->onDelete('cascade');
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
        Schema::dropIfExists('annotations');
    }
}
