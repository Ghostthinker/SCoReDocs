<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('ref_id', 255);
            $table->string('target')->comment('Target link');
            $table->string('origin')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->bigInteger('section_id')->comment('Used in section')->unsigned();
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
        Schema::dropIfExists('links');
    }
}
