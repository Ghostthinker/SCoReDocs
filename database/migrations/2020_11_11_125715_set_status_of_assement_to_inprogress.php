<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Section;
use App\Models\Project;

class SetStatusOfAssementToInprogress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projects = Project::where('type', 'AssessmentDoc')->with('sections')->get();
        foreach($projects as $project) {
            foreach($project->sections as $section) {
                $section->status = 1;
                $section->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            //
        });
    }
}
