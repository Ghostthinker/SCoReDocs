<?php

namespace App\Http\Controllers;

use App\Enums\ProjectType;
use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Events\NewProjectEvent;
use App\Events\UpdateProjectEvent;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectResourceCollection;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\ImageService;
use App\Services\LinkService;
use App\Services\ProjectService;
use App\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Xapi;

class ProjectController extends Controller
{
    /**
     * @param ProjectRepositoryInterface $projectRepository
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->allOfType(new ProjectType(ProjectType::PROJECT))->toArray();
        return view('pages.projects', $projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return ProjectResource
     */
    public function store(Request $request, ProjectRepositoryInterface $projectRepository)
    {
        $this->validator($request->all());
        $project = $projectRepository->create($request->all());
        $project->type = ProjectType::PROJECT;
        broadcast(new NewProjectEvent(ProjectResource::make($project)))->toOthers();
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_CREATED),
            $request->fullUrl(),
            ['en-US' => $project->title],
            $project->id,
            [
                url('/projectId') => $project->id,
                url('/revisionId') => $project->audits()->latest()->first() ? $project->audits()->latest()->first()->id : null,
            ]
        );

        return ProjectResource::make($project);
    }

    /**
     * @param Request $request
     * @param ProjectRepositoryInterface $projectRepository
     * @return mixed
     */
    public function getProjects(Request $request, ProjectRepositoryInterface $projectRepository)
    {
        $projects = $projectRepository->allOfType(new ProjectType(ProjectType::PROJECT));
        return new ProjectResourceCollection($projects);
    }

    /**
     * @param Request $request
     * @param $project_id
     * @param ProjectRepositoryInterface $projectRepository
     * @return mixed
     */
    public function getProject(Request $request, $project_id, ProjectRepositoryInterface $projectRepository)
    {
        $project = $projectRepository->findOrFail($project_id);
        return ProjectResource::make($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $project_id
     * @param ProjectRepositoryInterface $projectRepository
     * @return Response
     */
    public function update(Request $request, $project_id, ProjectRepositoryInterface $projectRepository, ProjectService $projectService)
    {
        $data = $request->all();
        $this->validator($data);
        $projectBefore = $projectRepository->get($project_id);
        $changeToArchive = false;
        if (array_key_exists('type', $data)) {
            $isTypeArchive = $data['type'] === ProjectType::ARCHIVED;
            $hasChangeType = $data['type'] !== $projectBefore->type;
            $changeToArchive = $isTypeArchive && $hasChangeType;
        }
        $success = $projectRepository->update($project_id, $data);
        $project = $projectRepository->get($project_id);
        broadcast(new UpdateProjectEvent(ProjectResource::make($project)));
        if ($success && $changeToArchive) {
            $projectService->sendMailProjectIsArchived($project);
        }
        return $success;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @param $project_id
     * @param ProjectRepositoryInterface $projectRepository
     * @return void
     */
    public function destroy(Project $project, $project_id, ProjectRepositoryInterface $projectRepository)
    {
        return $projectRepository->delete($project_id);
    }

    /**
     * @param  Request  $request
     * @param  Project  $project
     * @param  ImageService  $imageService
     * @param  LinkService  $linkService
     * @param  SectionService  $sectionService
     */
    public function duplicateProject(Request $request, Project $project, ImageService $imageService, LinkService $linkService, SectionService $sectionService)
    {
        if($project->type == ProjectType::TEMPLATE) {
            abort('422', 'Assessment Vorlagen können nicht dupliziert werden.');
        }

        $duplicateProject = $project->duplicate();
        if(!$duplicateProject){
            abort('404');
        }

        $duplicateProject->title = 'Kopie von Forschungsprojekt: "'.$duplicateProject->title.'"';
        $duplicateProject->save();

        //This step is important to create new refId tags for every media element => section_media table
        if($duplicateProject->sections){
            foreach($duplicateProject->sections as $section){
                $sectionService->updateRefIdOfSectionMedia($section, $request->fullUrl(), $imageService, $linkService);
            }
        }

        broadcast(new NewProjectEvent(ProjectResource::make($duplicateProject)));
    }

    public function toggleWatchProject(Project $project)
    {
        $user = Auth::user();
        try{
            $user->project_watch()->toggle($project->id);
        }catch (Throwable $exception) {
            abort('404');
        }
        return response('ok', 200);
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
        ])->setAttributeNames(['title' => 'Titel'])->validate();
    }
}
