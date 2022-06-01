<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinorUpdateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $column = 'is_minor_update';
            if(!Schema::hasColumn('audits', $column)){
                $table->boolean($column)->default(false)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $column = 'is_minor_update';
            if(Schema::hasColumn('audits', $column)){
                $table->dropColumn($column);
            }
        });
    }
}
