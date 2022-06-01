<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionsAuthor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->bigInteger('author_id')->unsigned()->nullable();
            $table->foreign('author_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });
    }
}
