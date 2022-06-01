<?php

namespace App\Http\Controllers;

use App\Enums\ProjectType;
use App\Http\Resources\ProjectResourceCollection;
use App\Repositories\ProjectRepositoryInterface;
use App\Rules\PermissionSet;
use Auth;
use Illuminate\Http\Request;

class ProjectArchiveController extends Controller
{
    /**
     * Returns all archived projects
     *
     * @param  Request  $request
     * @param  ProjectRepositoryInterface  $projectRepository
     * @return ProjectResourceCollection
     */
    public function getArchivedProjects(
        Request $request,
        ProjectRepositoryInterface $projectRepository
    ) {
        if (Auth::user()->can(PermissionSet::CAN_VIEW_ARCHIVE)) {
            $archivedProjects = $projectRepository->allOfType((new ProjectType(ProjectType::ARCHIVED)));
        } else {
            $archivedProjects = Auth::user()->project_involve()->getResults();
        }
        return new ProjectResourceCollection($archivedProjects);
    }
}
