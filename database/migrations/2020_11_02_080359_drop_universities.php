<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniversities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropForeign(['data_university_id']);
            });
        } catch(\BadMethodCallException $e) {
            // 011.2020 - 10:36 - BF - This is for the tests because sqlite doesn't support foreign keys
            // do something, or nothing: just ignore
        }
        Schema::table('data_universities', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('data_university_id');
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
