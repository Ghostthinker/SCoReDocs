<?php

use App\Models\Project;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToUserInvolvedProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_involved_projects', function (Blueprint $table) {
            $projects = Project::all();
            $users = User::all();

            foreach ($projects as $project) {
                $sections = $project->sections()->get();
                foreach ($users as $user) {
                    $userWasInvolvedInProject = false;
                    foreach ($sections as $section) {
                        $userWasInvolvedInProject = $section->audits()
                            ->where('user_id', $user->id)->exists();
                        if($userWasInvolvedInProject) {
                            break;
                        }
                    }
                    if($userWasInvolvedInProject) {
                        $user->project_involve()->attach($project->id);
                    }
                }
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
        Schema::table('user_involved_projects', function (Blueprint $table) {
            //
        });
    }
}
