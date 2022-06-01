<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MediaType;

class MediaAddAttribute360 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            if (!Schema::hasColumn('media', 'type')) {
                $table->integer('type')->default(MediaType::DEFAULT);
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
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}
