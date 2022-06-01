<?php

namespace App\Repositories;

use App\Enums\ProjectType;
use App\Models\Project;
use Throwable;

class ProjectRepository implements ProjectRepositoryInterface, EloquentRepositoryInterface
{
    public function get($instructionId)
    {
        return Project::find($instructionId);
    }

    public function findOrFail($projectId)
    {
        return Project::findOrFail($projectId);
    }

    public function all()
    {
        return Project::all();
    }

    public function allOfType($type)
    {
        return Project::ofType($type)->get();
    }

    public function allAssessmentDocsWithOwner()
    {
        return Project::ofType(new ProjectType(ProjectType::ASSESSMENT_DOC))->whereHas('assessmentDocOwner')->with('assessmentDocOwner', 'assessmentDocOwner.profile')->get();
    }

    public function delete($projectId)
    {
        Project::destroy($projectId);
    }

    public function update($projectId, array $data)
    {
        try {
            $project = Project::findOrFail($projectId);
        } catch (Throwable $exception) {
            return false;
        }

        try {
            $project->update($data);
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    public function create(array $data)
    {
        $project = Project::make($data);

        if ($project->save()) {
            return $project;
        }
    }
}
