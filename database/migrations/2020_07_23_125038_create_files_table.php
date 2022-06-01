<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('files', function(Blueprint $table) {
            $table->id();
            $table->string('filename')->default(\App\Enums\MediaStatus::CREATED);
            $table->integer('uid')->nullable();
            $table->string('storage')->nullable();
            $table->string('path')->nullable();
            $table->string('caption')->nullable();
            $table->integer('filesize')->nullable();
            $table->string('status')->nullable();
            $table->string('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('files');
    }
}
