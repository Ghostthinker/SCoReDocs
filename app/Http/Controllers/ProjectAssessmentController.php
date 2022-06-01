<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssessmentResource;
use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Http\Request;

class ProjectAssessmentController extends Controller
{
    /**
     * Returns all assessment docs
     *
     * @param Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAssessmentDocs(
        Request $request,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $assessmentDocs = $projectRepository->allAssessmentDocsWithOwner();
        return AssessmentResource::collection($assessmentDocs);
    }
}
