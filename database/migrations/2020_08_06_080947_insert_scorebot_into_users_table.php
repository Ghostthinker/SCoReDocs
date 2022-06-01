<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class InsertScorebotIntoUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')->insert(
            array(
                'name' => 'SCoRe-Bot',
                'email' => 'scorebot@score',
                'password' => Hash::make('B0tSc0R3'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            )
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
