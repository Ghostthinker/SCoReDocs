<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_media', function (Blueprint $table) {
            $table->id();
            $table->string('ref_id');
            $table->string('type')->nullable();
            $table->bigInteger('section_id')->comment('Used in section')->unsigned();
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
        Schema::dropIfExists('sections_media');
    }
}
