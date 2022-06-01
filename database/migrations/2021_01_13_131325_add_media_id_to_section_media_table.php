<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaIdToSectionMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_media', function (Blueprint $table) {
            $table->bigInteger('mediable_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_media', function (Blueprint $table) {
            if (Schema::hasColumn('section_media', 'mediable_id')) {
                $table->dropColumn('mediable_id');
            }
        });
    }
}
