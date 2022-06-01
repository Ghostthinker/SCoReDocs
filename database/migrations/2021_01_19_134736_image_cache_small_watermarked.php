<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImageCacheSmallWatermarked extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $str_sections_search = "%/small%";
        $str_sections_find = "/small";
        $str_sections_replace = "/smallWatermarked";

        DB::table('sections')
            ->where('content', "LIKE", $str_sections_search)
            ->update([
                "content" => DB::raw('REPLACE(content, "' . $str_sections_find . '", "' . $str_sections_replace . '")')
            ]);

        DB::table('sections')
            ->where('content', "LIKE", $str_sections_search)
            ->update([
                "content" => DB::raw('REPLACE(content, "smallWatermarkedWatermarked", "smallWatermarked")')
            ]);

        $str_search = '%/small%';
        $str_find = '/small';
        $str_replace = '/smallWatermarked';

        // /small -> /smallWatermarked
        DB::table('audits')
            ->where('state', "LIKE", $str_search)
            ->update([
                    "state" => DB::raw('REPLACE(state, "' . $str_find . '", "' . $str_replace . '")')
                ]
            );
        DB::table('audits')
            ->where('old_values', "LIKE", $str_search)
            ->update([
                    "old_values" => DB::raw('REPLACE(state, "' . $str_find . '", "' . $str_replace . '")')
                ]
            );
        DB::table('audits')
            ->where('new_values', "LIKE", $str_search)
            ->update([
                    "new_values" => DB::raw('REPLACE(state, "' . $str_find . '", "' . $str_replace . '")')
                ]
            );

        // smallWatermarkedWatermarked => smallWatermarked
        DB::table('audits')
            ->where('state', "LIKE", $str_search)
            ->update([
                    "state" => DB::raw('REPLACE(state, "smallWatermarkedWatermarked", "smallWatermarked")')
                ]
            );
        DB::table('audits')
            ->where('old_values', "LIKE", $str_search)
            ->update([
                    "old_values" => DB::raw('REPLACE(state, "smallWatermarkedWatermarked", "smallWatermarked")')
                ]
            );
        DB::table('audits')
            ->where('new_values', "LIKE", $str_search)
            ->update([
                    "new_values" => DB::raw('REPLACE(state, "smallWatermarkedWatermarked", "smallWatermarked")')
                ]
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
