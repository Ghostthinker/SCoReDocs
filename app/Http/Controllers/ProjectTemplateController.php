<?php

namespace App\Http\Controllers;

use App\Enums\ProjectType;
use App\Events\NewProjectEvent;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectResourceCollection;
use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ProjectTemplateController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return Response
     */
    public function store(Request $request, ProjectRepositoryInterface $projectRepository)
    {
        $templateCount = $projectRepository->allOfType(new ProjectType(ProjectType::TEMPLATE))->count();
        if ($templateCount == 1) {
            throw new UnprocessableEntityHttpException('Es kann nur ein Template angelegt werden');
        }

        $data = $request->all();
        $data['type'] = ProjectType::TEMPLATE;
        $this->validator($data);
        $project = $projectRepository->create($data);

        broadcast(new NewProjectEvent(ProjectResource::make($project)))->toOthers();

        return $project;
    }

    /**
     * @param Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return mixed
     */
    public function getAssessmentDocTemplate(Request $request, ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->allOfType(new ProjectType(ProjectType::TEMPLATE));
        return new ProjectResourceCollection($projects);
    }

    /**
     * @param Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return mixed
     */
    public function getProjectTemplates(Request $request, ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->allOfType(new ProjectType(ProjectType::PROJECT_TEMPLATE));
        return new ProjectResourceCollection($projects);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'string', 'max:191'],
            function ($attribute, $value, $fail) {
            },
        ])->setAttributeNames(['title' => 'Titel'])->validate();
    }
}
