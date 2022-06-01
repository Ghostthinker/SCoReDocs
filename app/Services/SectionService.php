<?php

namespace App\Services;

use App\Enums\SectionStatus;
use App\Events\DeleteSectionEvent;
use App\Events\NewSectionEvent;
use App\Events\ReloadSectionEvent;
use App\Events\UpdateSectionEvent;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Repositories\AuditRepositoryInterface;
use App\Repositories\SectionRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionStatusValidator;
use Auth;
use Illuminate\Support\Collection;
use Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SectionService
{
    private $sectionRepository;
    private $userRepository;
    private $auditRepository;
    private $activityService;

    public function __construct(SectionRepositoryInterface $sectionRepository,
        UserRepositoryInterface $userRepository,
        AuditRepositoryInterface $auditRepository,
        ActivityService $activityService
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
        $this->auditRepository = $auditRepository;
        $this->activityService = $activityService;
    }

    /**
     * @param int $projectId
     * @param array $data
     *
     * @return mixed
     */
    public function storeSection($projectId, $data)
    {
        $data = $this->validateStoreSectionData($projectId, $data);

        $sections = $this->sectionRepository->getAllByProjectId($projectId);
        Section::disableAuditing();
        $indexCollection = $this->updateSectionIndex($sections, $data['index']);
        Section::enableAuditing();
        $section = $this->sectionRepository->create($data);

        $section = SectionResource::make($section);

        broadcast(new NewSectionEvent($section->id, $section->project_id, $indexCollection->toJson()));
        return $section;
    }

    public function validateStoreSectionData($projectId, $data)
    {
        $isTopSection = array_key_exists('topSectionId', $data) && ((int) $data['topSectionId'] > 0);
        if ($isTopSection) {
            $sectionTop = $this->sectionRepository->get($data['topSectionId']);
            if ($sectionTop !== null) {
                $data['index'] = $sectionTop->index + 1;
            }
        } else {
            $data['index'] = 0;
        }
        unset($data['topSectionId']);

        if (!isset($data['project_id']) || $data['project_id'] != $projectId) {
            $data['project_id'] = $projectId;
        }
        if (!isset($data['heading'])) {
            $data['heading'] = Auth::getUser()->can([
                PermissionSet::CHANGE_HEADING_1_CONTENT, PermissionSet::CHANGE_HEADING_2_CONTENT,
            ]) ? '1' : '3';
        }
        if (!isset($data['title'])) {
            $data['title'] = 'Neuer Abschnitt';
        }
        if (!isset($data['content'])) {
            $data['content'] = '<p>Hier den Text editieren</p>';
        }
        if (!Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_WITH_EDIT_NOT_POSSIBLE_STATUS)) {
            $data['status'] = SectionStatus::IN_PROGRESS;
        }
        return $data;
    }

    /**
     * Method updates a section
     *
     * @param int $sectionId The section id to update
     * @param mixed $data The new data
     *
     * @return bool Success indicator
     */
    public function updateSection($sectionId, $data)
    {
        /* @var Section $section */
        $section = $this->sectionRepository->get($sectionId);

        if (isset($data['changeLog'])) {
            $section->setChangeLog($data['changeLog']);
            unset($data['changeLog']);
        }

        $section->withMinorUpdate(false);
        if (isset($data['isMinorUpdate'])) {
            $section->withMinorUpdate($data['isMinorUpdate']);
            unset($data['isMinorUpdate']);
        }

        // Update childs or parents depending on state change
        if (isset($data['status']) && ($data['status'] != $section->status)) {
            $success = $this->updateAndBroadcastStatus($section, $data['status']);
            if ($success) {
                $this->broadcastStatusChangedEvent($section);
            }
        }
        $success = $this->sectionRepository->update($section, $data);
        if ($success) {
            $this->broadcastUpdate($section->id, false, true);
        }
        return $success;
    }

    /**
     * Method updates a section
     *
     * @param       $section
     * @param mixed $data The new data
     *
     * @return bool Success indicator
     */
    public function updateSectionWithIndex($section, $data)
    {
        $sectionTop = $this->sectionRepository->get($data['topSectionId']);
        if ($sectionTop !== null) {
            $data['index'] = $sectionTop->index + 1;
        } else {
            $data['index'] = 0;
        }
        unset($data['topSectionId']);

        $sections = $this->sectionRepository->getAllByProjectId($section->project_id);
        Section::disableAuditing();
        $indexCollection = $this->updateSectionIndex($sections, $data['index']);
        $success = $this->sectionRepository->update($section, $data);
        Section::enableAuditing();
        if ($success) {
            broadcast(new NewSectionEvent($section->id, $section->project_id, $indexCollection->toJson()));
        }
        return $success;
    }

    /**
     * @param $sectionId
     * @param $userId
     *
     * @return false
     */
    public function lockSection($sectionId, $userId)
    {
        $section = $this->sectionRepository->get($sectionId);
        if ($section->locking_user !== null) {
            throw new AccessDeniedHttpException();
        }

        $data = [
            'locked' => true,
            'locked_at' => now(),
            'locking_user' => $userId,
        ];
        Section::disableAuditing();
        $updateSuccess = $this->sectionRepository->update($section, $data);
        Section::enableAuditing();
        return $updateSuccess;
    }

    /**
     * @param $sectionId
     * @param $userId
     *
     * @return false
     */
    public function unLockSection($sectionId, $userId = null)
    {
        $section = $this->sectionRepository->get($sectionId);
        if ($userId !== null) {
            if ($section->locking_user != $userId) {
                return false;
            }
        }

        $data = [
            'locked' => false,
            'locked_at' => null,
            'locking_user' => null,
        ];
        Section::disableAuditing();
        $updateSuccess = $this->sectionRepository->update($section, $data);
        Section::enableAuditing();
        return $updateSuccess;
    }

    /**
     * @param $sectionId
     * @param $data
     *
     * @return bool
     */
    public function destroySection($sectionId, $data)
    {
        $section = $this->sectionRepository->get($sectionId);
        if ($section === null) {
            return false;
        }

        if (!isset($data['changeLog'])) {
            return false;
        }

        $success = $this->sectionRepository->delete($sectionId);
        if ($success) {
            Log::info('Deleted section success, id => ' . $sectionId);
            $audit = $section->audits->last();
            // audit must set changelog manuel of deleted models
            $this->auditRepository->update($audit->id, ['change_log' => $data['changeLog']]);

            broadcast(new DeleteSectionEvent($section->id, $section->project_id));
            $this->broadcastDeletedSectionMessageEvent($section, $data['changeLog']);
        }
        return $success;
    }

    /**
     * Restore a Section
     *
     * @param int $section_id
     * @param int $top_section_id
     * @return mixed
     */
    public function restoreSection(int $section_id, int $top_section_id)
    {
        $section = $this->sectionRepository->withTrashed($section_id);
        $section->setChangeLog('Abschnitt wurde wiederhergestellt');

        $data = [];
        $data['topSectionId'] = $top_section_id;
        $successUpdate = $this->updateSectionWithIndex($section, $data);
        $successRestore = $section->restore();

        if (!$successRestore) {
            return $successRestore;
        }
        $this->broadcastRevertedSectionMessageEvent($section);
        return $successUpdate;
    }

    /**
     * Returns a list of possible status for a section
     *
     * @param $sectionId
     *
     * @return Collection
     */
    public function getPossibleSectionStatus($sectionId)
    {
        $statusCollection = collect();
        foreach (SectionStatus::toArray() as $status) {
            $statusCollection->push([
                'status' => $status,
                'statusText' => SectionStatus::getDescription($status),
                'allowed' => $this->isSectionStatusAllowed($status, $sectionId),
            ]);
        }
        return $statusCollection;
    }


    public function updateRefIdOfSectionMedia(Section $section, $requestUrl, ImageService $imageService, LinkService $linkService)
    {
        $section->content = preg_replace('/ ref="(.*?)"/', '', $section->content);
        $section->content = $imageService->addIdsToImages($section->content, $section->id, $requestUrl, true);
        $section->content = $linkService->parseLocalLinks($section->content, $section, $section->project_id, $requestUrl, true);

        $section->save();
    }

    private function isSectionStatusAllowed($status, $sectionId)
    {
        $section = $this->sectionRepository->get($sectionId);
        $validator = new SectionStatusValidator($section);
        return $validator->passes('status', $status);
    }

    /**
     * Updates a section status and their children or parents depending on status change
     *
     * @param Section $section The section to update
     * @param int $newStatus The new Status
     *
     * @return bool Indicator if update was successful
     */
    private function updateAndBroadcastStatus($section, $newStatus)
    {
        // If status is submitted and is changed to inProgress make parents inProgress, too
        if ($section->status == SectionStatus::SUBMITTED && $newStatus == SectionStatus::IN_PROGRESS) {
            foreach ($section->getParents() as $tmpSection) {
                $tmpSection->withMinorUpdate($section->isMinorUpdate());
                $this->updateBroadcastStatusSubmitted($tmpSection);
            }
        } else {
            foreach ($section->getChildren() as $tmpSection) {
                $tmpSection->withMinorUpdate($section->isMinorUpdate());
                $this->updateBroadcastStatusLocked($tmpSection, $newStatus);
            }
        }
        return $this->sectionRepository->update($section, array_merge(['status' => $newStatus]));
    }

    private function updateBroadcastStatusSubmitted($tmpSection)
    {
        if ($tmpSection->status != SectionStatus::SUBMITTED) {
            return false;
        }
        $changelog = 'In Bearbeitung gesetzt da der Status eines seiner Unterabschnitte manuell auf
         "In Bearbeitung" geändert wurde';
        $tmpSection->setChangeLog($changelog);
        $this->sectionRepository->update($tmpSection, array_merge(['status' => SectionStatus::IN_PROGRESS]));
        $this->broadcastUpdate($tmpSection->id, true, false);
        return true;
    }

    private function updateBroadcastStatusLocked($tmpSection, $newStatus)
    {
        $lockedData = ['locked' => 0, 'locking_user' => null, 'locked_at' => null];
        $changelog = 'In Bearbeitung nicht möglich gesetzt, da der Status des Elternabschnitts mit Überschrift 1
         manuell auf "Bearbeitung nicht möglich" geändert wurde';

        $tmpSection->setChangeLog($changelog);
        $this->sectionRepository->update($tmpSection, array_merge(['status' => $newStatus], $lockedData));
        $this->broadcastUpdate($tmpSection->id, true, true);
    }

    /**
     * Broadcast the update of a section to the clients
     *
     * @param  int  $sectionId  The section to broadcast
     * @param  false  $automaticUpdate  Should the section be updated at the clients? Is used for all child or parent sections
     * @param  false  $reloadSection  Should the section be reloaded at the clients?
     * @param  bool  $minorUpdate
     */
    private function broadcastUpdate($sectionId, $automaticUpdate = false, $reloadSection = false)
    {
        $section = $this->sectionRepository->get($sectionId);
        $this->broadcastUpdatedSectionEvent($section, $reloadSection);
        $this->broadcastUpdatedSectionMessageEvent($section, $automaticUpdate);
    }

    /**
     * Method broadcasts a status change
     *
     * @param Section $section The section which should be broadcast
     */
    private function broadcastStatusChangedEvent($section)
    {
        $audit = $section->audits->last();
        if(!$audit) { return; }
        if($audit->is_minor_update) { return; }

        $changeUserName = 'einem Anwender';
        if ($audit && $audit->user_id !== null) {
            $user = $this->userRepository->getFirst($audit->user_id);
            $changeUserName = $user !== null ? $user->name : $changeUserName;
            $changeMessage = 'Status des Abschnitts /' . $section->title .
                '/ wurde von "' . $changeUserName . ' zu "' . SectionStatus::getDescription($section->status) . '" geändert';

            $this->activityService->createAndBroadcastActivity($audit->user_id, $changeMessage, $section->id, $section->project_id);
        }
    }

    /**
     * Method broadcasts a section deleted
     *
     * @param Section $section The section which should be broadcast
     * @param String  $changeLog
     */
    private function broadcastDeletedSectionMessageEvent($section, $changeLog)
    {
        $audit = $section->audits->last();
        $changeUserName = 'einem Anwender';
        if ($audit->user_id !== null) {
            $user = $this->userRepository->getFirst($audit->user_id);
            $changeUserName = $user !== null ? $user->name : $changeUserName;
        }
        $changeMessage = 'Der Abschnitt /"' . $section->title .
            '"/ wurde von ' . $changeUserName . ' aus folgendem Grund "' . $changeLog . '" gelöscht!';

        $this->activityService->createAndBroadcastActivity($audit->user_id, $changeMessage, $section->id, $section->project_id);
    }

    /**
     * Method broadcasts a section reverted message
     *
     * @param Section $section The section which is reverted
     */
    private function broadcastRevertedSectionMessageEvent(Section $section)
    {
        $audit = $section->audits->last();
        $changeUserName = 'einem Anwender';
        if ($audit->user_id !== null) {
            $user = $this->userRepository->getFirst($audit->user_id);
            $changeUserName = $user !== null ? $user->name : $changeUserName;
        }
        $changeMessage = 'Der Abschnitt /"' . $section->title .
            '"/ wurde von ' . $changeUserName . ' wiederhergestellt';

        $this->activityService->createAndBroadcastActivity($audit->user_id, $changeMessage, $section->id, $section->project_id);
    }

    /**
     * Broadcasting a Message Event containing section audit information
     *
     * @param $section
     * @param  bool  $automaticUpdate
     * @param  bool  $minorUpdate
     */
    private function broadcastUpdatedSectionMessageEvent($section, $automaticUpdate = false)
    {
        $audit = $section->audits->last();
        if(!$audit) { return; }
        if($audit->is_minor_update) { return; }

        $changeUserName = 'Ein Anwender';
        if ($audit->user_id !== null) {
            $user = $this->userRepository->getFirst($audit->user_id);
            $changeUserName = $user !== null ? $user->name : $changeUserName;
        }
        if ($automaticUpdate) {
            $changeMessage = 'Status des Abschnitts /' . $section->title .
                '/ automatisch zu "' . SectionStatus::getDescription($section->status) . '" geändert';
        } else {
            $changeMessage = $changeUserName . ' hat eine neue Version von "'
                . $section->title . '" erstellt. Änderungsbeschreibung: "' . $audit->change_log . '"';
        }

        $this->activityService->createAndBroadcastActivity($audit->user_id, $changeMessage, $section->id, $section->project_id);
    }

    /**
     * Broadcasting a UpdateSectionEvent
     *
     * @param $section
     * @param bool $reloadSection
     */
    private function broadcastUpdatedSectionEvent($section, $reloadSection = false)
    {
        $section = SectionResource::make($section)->actualUser(Auth::id());
        if ($reloadSection) {
            broadcast(new ReloadSectionEvent($section->id, $section->project_id));
        } else {
            broadcast(new UpdateSectionEvent($section));
        }
    }

    /**
     * Updates the section indexes of a project
     *
     * @param $sections
     * @param $index
     *
     * @return Collection
     */
    private function updateSectionIndex($sections, $index)
    {
        $indexCollection = collect();
        foreach ($sections as $section) {
            if ($section->index < $index) {
                continue;
            }
            $section->index += 1;
            $section->save();
            $indexCollection->push([
                'id' => $section->id,
                'index' => $section->index,
            ]);
        }
        return $indexCollection;
    }
}
