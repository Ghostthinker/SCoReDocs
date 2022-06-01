<?php

namespace App\Http\Controllers;

use App\Enums\ProjectType;
use App\Events\SectionLockEvent;
use App\Events\UserWatchesProjectEvent;
use App\Http\Resources\AuditResource;
use App\Http\Resources\EP5\PlaylistResource;
use App\Http\Resources\SectionDeletedResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SectionResourceCollection;
use App\Models\Section;
use App\Repositories\AuditRepositoryInterface;
use App\Repositories\MediaRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\SectionRepositoryInterface;
use App\Repositories\SectionTrashRepositoryInterface;
use App\Repositories\TimeoutRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionContentValidator;
use App\Rules\Section\SectionCreateValidator;
use App\Rules\Section\SectionDeleteValidator;
use App\Rules\Section\SectionHeadingTypeValidator;
use App\Rules\Section\SectionStatusValidator;
use App\Services\EvoliService;
use App\Services\ImageService;
use App\Services\LinkService;
use App\Services\ProjectService;
use App\Services\PlayListService;
use App\Services\SectionService;
use App\Services\Xapi\XapiSectionService;
use Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class SectionController extends Controller
{
    use ValidatesRequests;
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $projectId
     * @param SectionService $sectionService
     * @param SectionRepositoryInterface $sectionRepository
     * @param ProjectRepositoryInterface $projectRepository
     *
     * @return SectionResource
     * @throws ValidationException
     */
    public function store(
        Request $request,
        $projectId,
        SectionService $sectionService,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $data = $request->all();
        $project = $projectRepository->findOrFail($projectId);
        $isTopSection = array_key_exists('topSectionId', $data) && ((int) $data['topSectionId'] > 0);
        if ($isTopSection) {
            $topSection = $sectionRepository->get($data['topSectionId']);
            $this->validate($request, ['topSectionId' => new SectionCreateValidator($topSection, $project)]);
        }
        $data['author_id'] = Auth::id();
        $sectionResource = $sectionService->storeSection($projectId, $data);
        if ($sectionResource) {
            if (!$project->isAuthUserWatching()) {
                Auth::user()->project_watch()->toggle($project->id);
                broadcast(new UserWatchesProjectEvent(Auth::user()));
            }
            Auth::user()->project_involve()->syncWithoutDetaching($project->id);
            XapiSectionService::storeSection($request->fullUrl(), $sectionResource, $project);
        }
        return $sectionResource->id;
    }

    /**
     * @param Request $request
     * @param                            $projectId
     * @param                            $sectionId
     * @param SectionRepositoryInterface $sectionRepository
     *
     * @return mixed
     */
    public function getSection(Request $request, $projectId, $sectionId, SectionRepositoryInterface $sectionRepository)
    {
        $section = $sectionRepository->get($sectionId);
        return SectionResource::make($section)->actualUser(Auth::id());
    }

    /**
     * @param Request $request
     * @param                            $projectId
     * @param                            $sectionId
     * @param SectionRepositoryInterface $sectionRepository
     *
     * @return mixed
     */
    public function getDeletedSection(Request $request, $projectId, $sectionId, SectionRepositoryInterface $sectionRepository)
    {
        $section = $sectionRepository->withTrashed($sectionId);
        return SectionResource::make($section)->actualUser(Auth::id());
    }

    /**
     * Get sections for a project
     *
     * @param Request $request
     * @param                            $projectId
     * @param SectionRepositoryInterface $sectionRepository
     * @param ProjectRepositoryInterface $projectRepository
     *
     * @return mixed
     */
    public function getSections(Request $request, $projectId,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $full = $request->query('items', null);
        $project = $projectRepository->findOrFail($projectId);

        // Check if user is allowed to get sections of project with type template
        if ($project->type == ProjectType::TEMPLATE && !Auth::getUser()->can(PermissionSet::EDIT_TEMPLATES)) {
            throw new AccessDeniedHttpException();
        }
        // Check if user is allowed to get sections of project with type archived
        if ($project->type == ProjectType::ARCHIVED && (!Auth::getUser()->can(PermissionSet::CAN_VIEW_ARCHIVE )
                && !Auth::user()->project_involve()->wherePivot('project_id', $project->id)->exists())) {
            throw new AccessDeniedHttpException();
        }
        // Check if user is allowed to get sections of project with type project template
        if ($project->type == ProjectType::PROJECT_TEMPLATE && !Auth::getUser()->can(PermissionSet::EDIT_TEMPLATES)) {
            throw new AccessDeniedHttpException();
        }
        // Check if user is allowed to get sections of project with type assessment_doc
        if ($project->type == ProjectType::ASSESSMENT_DOC && !Auth::getUser()->assessment_doc_id == $project->id) {
            if (!Auth::getUser()->can(PermissionSet::EDIT_ALL_ASSESSMENT_DOCS)) {
                throw new AccessDeniedHttpException();
            }
        }

        if ($full !== null) {
            $sections = $sectionRepository->getAllByProjectIdWithProjectAndUserCollapse($projectId);
        } else {
            $sections = $sectionRepository->getAllByProjectIdWithProjectPaginate($projectId);
        }
        XapiSectionService::getSections($request->fullUrl(), $project);
        return SectionResourceCollection::make($sections)->actualUser(Auth::id());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param                            $project_id
     * @param                            $section_id
     * @param SectionService $sectionService
     * @param LinkService $linkService
     * @param ImageService $imageService
     * @param ProjectService $projectService
     * @param SectionRepositoryInterface $sectionRepository
     * @param ProjectRepositoryInterface $projectRepository
     *
     * @param UserRepositoryInterface $userRepository
     * @return bool
     * @throws ValidationException
     */
    public function update(
        Request $request,
        $project_id,
        $section_id,
        SectionService $sectionService,
        LinkService $linkService,
        ImageService $imageService,
        ProjectService $projectService,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository,
        UserRepositoryInterface $userRepository
    ) {
        $data = $request->all();
        $this->validator($data);

        $oldSection = $sectionRepository->get($section_id);
        $this->validate($request, ['status' => new SectionStatusValidator($oldSection)]);
        $this->validate($request, ['content' => new SectionContentValidator($oldSection)]);
        $this->validate($request, ['title' => new SectionContentValidator($oldSection)]);
        $this->validate($request, ['heading' => new SectionHeadingTypeValidator($oldSection)]);

        $changedProperties = [];
        if(array_key_exists('status', $data) && $oldSection->status != $data['status']){
            array_push($changedProperties, 'status');
        }
        if(array_key_exists('content', $data) && $oldSection->content != $data['content']){
            array_push($changedProperties, 'content');
        }
        if(array_key_exists('title', $data) && $oldSection->title != $data['title']){
            array_push($changedProperties, 'title');
        }
        if(array_key_exists('heading', $data) && $oldSection->heading != $data['heading']){
            array_push($changedProperties, 'heading');
        }
        $isMinorUpdate = false;
        if (array_key_exists('isMinorUpdate', $data) && $data['isMinorUpdate'] == true) {
            $isMinorUpdate = true;
        }

        if (array_key_exists('content', $data)) {
            $data['content'] = $imageService->addIdsToImages($data['content'], $section_id, $request->fullUrl());
            $data['content'] = $linkService->parseLocalLinks($data['content'], $oldSection, $project_id, $request->fullUrl());
        }
        $success = $sectionService->updateSection($section_id, $data);
        if ($success) {
            $project = $projectRepository->findOrFail($project_id);
            if ($project->type == ProjectType::ASSESSMENT_DOC) {
                $newStatus = $projectService->updateAssessmentDocStatus($project, $oldSection->status, $data['status']);
                $projectService->sendAssessmentDocStatusChangeMails($project->status, $newStatus, $project);
            }
            if (!$project->isAuthUserWatching()) {
                Auth::user()->project_watch()->toggle($project->id);
                broadcast(new UserWatchesProjectEvent(Auth::user()));
            }

            Auth::user()->project_involve()->syncWithoutDetaching($project->id);

            $audit = $sectionRepository->getLastAudit($section_id);
            XapiSectionService::updateSection($request->fullUrl(), $data, $section_id, $project, $audit, $changedProperties);
            if ($isMinorUpdate) {
                XapiSectionService::clickMinorUpdate($request->fullUrl(), $oldSection, $project, $audit);
            }
        }
        return $success;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request                    $request
     * @param                            $project_id
     * @param                            $section_id
     * @param SectionService             $sectionService
     * @param SectionRepositoryInterface $sectionRepository
     * @param TimeoutRepositoryInterface $timeoutRepository
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(
        Request $request,
        $project_id, $section_id,
        SectionService $sectionService,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository,
        TimeoutRepositoryInterface $timeoutRepository
    ) {
        $section = $sectionRepository->get($section_id);
        $project = $projectRepository->get($project_id);

        XapiSectionService::destroySection($request->fullUrl(), $project, $section);
        // not correct execute
        //$this->validate($request, ['delete' => new SectionDeleteValidator($section)]);

        $validator = new SectionDeleteValidator($section);
        if (!$validator->passes('delete', $section)) {
            throw new HttpException(403, $validator->getMessage());
        }

        $data = $request->all();
        try {
            $timeoutRepository->deleteBySectionId($section_id);
        } catch (Throwable $exception) {
            \Log::info('No timeout found to clear. Error: ' . $exception->getMessage());
        }
        $sectionService->unLockSection($section_id);
        if (!$sectionService->destroySection($section_id, $data)) {
            throw new HttpException(304);
        }
        return Response::noContent();
    }

    /**
     * @param Request                    $request
     * @param                            $project_id
     * @param                            $section_id
     * @param SectionService             $sectionService
     * @param TimeoutRepositoryInterface $timeoutRepository
     *
     * @return mixed
     */
    public function lock(Request $request,
        $project_id,
        $section_id,
        SectionService $sectionService,
        TimeoutRepositoryInterface $timeoutRepository)
    {
        $sectionService->lockSection($section_id, Auth::id());
        try {
            $timeoutRepository->createBySectionId($section_id);
        } catch (Throwable $exception) {
            Log::error('Creation of timeout was not successful. Continuing as normal. Error: '.$exception->getMessage());
        }
        broadcast(new SectionLockEvent($section_id, $project_id))->toOthers();
        return response('locked');
    }

    /**
     * @param Request                    $request
     * @param                            $project_id
     * @param                            $section_id
     * @param SectionService             $sectionService
     * @param TimeoutRepositoryInterface $timeoutRepository
     *
     * @return mixed
     */
    public function unlock(Request $request,
        $project_id,
        $section_id,
        SectionService $sectionService,
        TimeoutRepositoryInterface $timeoutRepository)
    {
        $sectionService->unLockSection($section_id, Auth::id());
        try {
            $timeoutRepository->deleteBySectionId($section_id);
        } catch (Throwable $exception) {
            \Log::error('Error on deleting the timeout. Continuing as normal. Error message:');
            \Log::error($exception->getMessage());
        }
        broadcast(new SectionLockEvent($section_id, $project_id))->toOthers();
        return response('unlocked');
    }

    /**
     * @param Request $request
     * @param $project_id
     * @param $section_id
     * @param SectionService $sectionService
     * @return mixed
     */
    public function getPossibleSectionStatus(Request $request, $project_id, $section_id, SectionService $sectionService)
    {
        return $sectionService->getPossibleSectionStatus($section_id)->toJson();
    }

    /**
     * Request returns the audits for a specific section
     *
     * @param Request                  $request The request
     * @param int                      $project_id The project id
     * @param int                      $section_id The section id
     * @param AuditRepositoryInterface $repository
     *
     * @return array The audits as json
     */
    public function getAudits(Request $request, int $project_id, int $section_id, AuditRepositoryInterface $repository)
    {
        $offset = $request->query('offset', 0);
        $limit = $request->query('limit', 100);

        $total = $repository->getCountBy($section_id);
        $audits = $repository->getBy($section_id, $offset, $limit);

        $collection = AuditResource::collection($audits);
        return [
            'count' => count($collection),
            'total' => $total,
            'audits' => $collection,
        ];
    }

    /**
     * Return all deleted sections of a project
     *
     * @param Request                         $request The request
     * @param int                             $project_id
     * @param SectionTrashRepositoryInterface $repository
     *
     * @return array The sections as json
     */
    public function getDeletedSections(Request $request, int $project_id, SectionTrashRepositoryInterface $repository)
    {
        $offset = $request->query('offset', 0);
        $limit = $request->query('limit', 100);

        $total = $repository->getCountBy($project_id);
        $sections = $repository->getBy($project_id, $offset, $limit);

        $collection = SectionDeletedResource::collection($sections);
        return [
            'total' => $total,
            'sections' => $collection,
        ];
    }

    /**
     * Reverts a section; sets new index and broadcasts section to clients
     *
     * @param  Request  $request
     * @param  int  $project_id
     * @param  int  $section_id
     * @param  int  $top_section_id
     * @param  SectionService  $sectionService
     * @param  ProjectRepositoryInterface  $projectRepository
     * @param  SectionRepositoryInterface  $sectionRepository
     * @return void
     */
    public function revertSection(
        Request $request,
        int $project_id,
        int $section_id,
        int $top_section_id,
        SectionService $sectionService,
        ProjectRepositoryInterface $projectRepository,
        SectionRepositoryInterface $sectionRepository
    ) {
        $sectionService->restoreSection($section_id, $top_section_id);
        $project = $projectRepository->get($project_id);
        $section = $sectionRepository->get($section_id);
        XapiSectionService::revertSection($request->fullUrl(), $project, $section);
    }

    /**
     * @param  Request  $request
     * @param $section_id
     * @param  PlayListService  $playListService
     * @param  SectionRepositoryInterface  $sectionRepository
     * @param  ProjectRepositoryInterface  $projectRepository
     * @return PlaylistResource
     */
    public function getPlaylist(
        Request $request,
        $section_id,
        PlayListService $playListService,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository)
    {
        $section = $sectionRepository->findOrFail($section_id);
        $project = $projectRepository->get($section->project_id);
        $playListItems = $playListService->createPlayListArray($section->content);

        XapiSectionService::openedPlaylist($request->fullUrl(), $section, $project);

        return PlaylistResource::make($playListItems)->sectionTitle($section->title);
    }

    /**
     * downloads the playlist as one video or
     * starts the conversion when the video is not in cache
     *
     * @param  Request  $request
     * @param $section_id
     * @param  EvoliService  $evoliService
     * @param  PlayListService  $playListService
     * @param  SectionRepositoryInterface  $sectionRepository
     * @param  ProjectRepositoryInterface  $projectRepository
     * @param  MediaRepositoryInterface  $mediaRepository
     * @return array|\Illuminate\Http\Client\Response
     * @throws \App\Exceptions\EvoliException
     */
    public function downloadPlaylist(
        Request $request,
        $section_id,
        EvoliService $evoliService,
        PlayListService $playListService,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository,
        MediaRepositoryInterface $mediaRepository)
    {
        $section = $sectionRepository->findOrFail($section_id);
        $project = $projectRepository->get($section->project_id);
        $playListItems = $playListService->createPlayListArray($section->content);

        // build data
        $medias = [];

        foreach ($playListItems as $index=>$item) {
            // check if sequence
            if($item->timestamp || $item->timestamp === 0)
            {
                $id = $item->streamingId;

                if($id === null) {
                    $media =  $mediaRepository->get($item->video_nid);
                    $id = $media->streamingId;
                }

                $mediasItem = [
                    'id' => $id,
                    'pos' => $index,
                    'start' => $item->timestamp * 0.001,
                    'end' => ($item->timestamp + $item->duration) * 0.001,
                    'yaw' => $item->camera_yaw,
                    'pitch' => $item->camera_pitch,
                    'type' => $item->camera_yaw !== null ? 'clip' : 'sequence'
                ];
            } else if($item->type !== 1) {
                $mediasItem = [
                    'id' => $item->streamingId,
                    'pos' => $index,
                    'type' => 'full',
                ];
            } else {
                $mediasItem = [
                    'id' => $item->streamingId,
                    'pos' => $index,
                    'type' => 'full',
                    'yaw' => -90,
                    'pitch' => 0,
                ];
            }
            $medias[] = $mediasItem;
        }

        $url = $evoliService->getApiEndpoint('download/concatenate');
        $result = $evoliService->postDownloadPlaylist($url, ['medias' => $medias]);

        if($result['status'] == 200) {
            XapiSectionService::downloadedPlaylist($request->fullUrl(), $section, $project);
        }

        return $result;
    }

    /**
     * gets the playlist conversion progress
     *
     * @param  Request  $request
     * @param $section_id
     * @param $playlist_id
     * @param  EvoliService  $evoliService
     * @return array|mixed
     */
    public function getPlaylistProgress(
        Request $request,
        $section_id,
        $playlist_id,
        EvoliService $evoliService)
    {
        return $evoliService->getPlaylistProgress($playlist_id);
    }

    /**
     * @param  Request  $request
     * @param $section_id
     * @param $playlist_id
     * @param  EvoliService  $evoliService
     * @return string
     */
    public function getPlaylistDownloadUrl(
        Request $request,
        $section_id,
        $playlist_id,
        EvoliService $evoliService)
    {
        return $evoliService->getPlaylistDownloadUrl($playlist_id);
    }

    public function openSection(Request $request, $project_id, Section $section)
    {
        Auth::user()->section_collapse()->detach($section->id);
        XapiSectionService::expandedSection($request->fullUrl(), $section, $project_id);
    }

    public function closeSection(Request $request, $project_id, Section $section)
    {
        Auth::user()->section_collapse()->attach($section->id);
        XapiSectionService::collapsedSection($request->fullUrl(), $section, $project_id);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return array
     * @throws ValidationException
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'string', 'max:191'],
        ])->setAttributeNames([
            'title' => 'Titel',
        ])->validate();
    }

}
