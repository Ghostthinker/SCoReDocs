<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLockTimeoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lock_timeouts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('section_id')->comment('Used in section')->unsigned()->unique();
            $table->foreign('section_id')
                ->references('id')
                ->on('sections');
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
        Schema::dropIfExists('lock_timeouts');
    }
}
