<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceIdToAnnotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('annotations', function (Blueprint $table) {
            $table->bigInteger('sequence_id')->unsigned()->nullable();
            $table->foreign('sequence_id')
                ->references('id')
                ->on('video_sequences');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annotations', function (Blueprint $table) {
            if (Schema::hasColumn('annotations', 'sequence_id')) {
                $table->dropColumn('sequence_id');
            }
        });
    }
}
