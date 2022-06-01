<?php

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Repositories\UserRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('status')->nullable();
            $table->bigInteger('assessment_doc_owner_id')->unsigned()->nullable();
            $table->foreign('assessment_doc_owner_id')
                ->references('id')
                ->on('users');
        });

        $projects = Project::where('type', 'AssessmentDoc')->get();
        $userRepository = new UserRepository();

        foreach($projects as $project) {
            $user = $userRepository->getUserFromAssessmentDocId($project->id);

            $project->status = ProjectStatus::IN_PROGRESS;
            $project->assessment_doc_owner_id = $user->id;
            $project->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['assessment_doc_owner_id']);
            if (Schema::hasColumn('projects', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('projects', 'assessment_doc_owner_id')) {
                $table->dropColumn('assessment_doc_owner_id');
            }
        });
    }
}
