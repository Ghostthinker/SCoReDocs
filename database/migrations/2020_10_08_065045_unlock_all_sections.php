<?php

use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

class UnlockAllSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $lockedSections = Section::where('locked', true)->get();
        foreach($lockedSections as $section) {
            $section->locked = false;
            $section->locked_at = null;
            $section->locking_user = null;
            $section->save();
        }
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
