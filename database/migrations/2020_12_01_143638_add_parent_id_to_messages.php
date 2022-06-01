<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['section']);
            });
        } catch (BadMethodCallException $e) {
        }
        Schema::table('messages', function (Blueprint $table) {
            $table->bigInteger('parent_id')->unsigned()->nullable();
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('messages');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->renameColumn('section', 'section_id');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('section_id')
                ->references('id')
                ->on('sections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            if (Schema::hasColumn('messages', 'parent_id')) {
                $table->dropColumn('parent_id');
            }
            $table->dropForeign(['section_id']);
            $table->renameColumn('section_id', 'section');
            $table->foreign('section')
                ->references('id')
                ->on('sections');
        });
    }
}
