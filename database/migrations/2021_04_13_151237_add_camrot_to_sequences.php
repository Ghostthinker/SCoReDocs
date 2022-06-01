<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamrotToSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_sequences', function (Blueprint $table) {
            $table->float('camera_yaw')->nullable();
            $table->float('camera_pitch')->nullable();
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
            $table->dropColumn('camera_yaw');
            $table->dropColumn('camera_pitch');
        });
    }
}
