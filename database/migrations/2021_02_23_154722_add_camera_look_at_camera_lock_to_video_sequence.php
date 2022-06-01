<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCameraLookAtCameraLockToVideoSequence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_sequences', function (Blueprint $table) {
            $table->string('camera_look_at')->nullable();
            $table->boolean('camera_locked')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_sequences', function (Blueprint $table) {
            $table->dropColumn('camera_look_at');
            $table->dropColumn('camera_locked');
        });
    }
}
