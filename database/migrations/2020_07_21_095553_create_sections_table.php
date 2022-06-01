<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->text('content')->nullable();
            $table->integer('heading');
            $table->integer('index');
            $table->boolean('locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->bigInteger('locking_user')->unsigned()->nullable();
            $table->foreign('locking_user')
                ->references('id')
                ->on('users');
            $table->bigInteger('project_id')->unsigned();
            $table->foreign('project_id')
                ->references('id')
                ->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
