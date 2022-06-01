<?php

namespace App\Services;

use App\Events\MessageMentionEvent;
use App\Repositories\MessageMentioningsRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UserRepository;
use App\Services\Xapi\XapiChatService;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    private $messageMentioningsRepository;
    private $projectRepository;
    private $sectionRepository;
    private $userRepository;
    private $messageRepository;

    public function __construct(
        MessageMentioningsRepository $messageMentioningsRepository,
        SectionRepository $sectionRepository,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        MessageRepository $messageRepository
    ) {
        $this->messageMentioningsRepository = $messageMentioningsRepository;
        $this->projectRepository = $projectRepository;
        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @param $request
     * @param $messageId
     * @param $sectionId
     * @param $projectId
     */
    public function createAtAllMentioning($request, $messageId, $sectionId, $projectId)
    {
        $project = $this->projectRepository->get($projectId);
        $section = $this->sectionRepository->get($sectionId);

        if ($section !== null) {
            $parent = $section;
            $parentId = url('/sectionId/'.$section->id);
        } else {
            $parent = $project;
            $parentId = url('/projectId/'.$project->id);
        }

        $participants = $this->getParticipantsByProject($project)->filter(function ($value, $key) {
            return $value->showUserInParticipantList == true;
        });;
        foreach ($participants as $mentionedUser){
            if ($mentionedUser->surname == 'SCoRe-Bot' || Auth::id() === $mentionedUser->id) {
                continue;
            }
            $mentioning = $this->messageMentioningsRepository->create([
                'user_id' => $mentionedUser->id,
                'message_id' => $messageId,
                'project_id' => $projectId
            ]);
            broadcast(new MessageMentionEvent($mentioning->id, $messageId, $projectId, $mentionedUser->id, $parent->title,
                $sectionId))->toOthers();
        }

        $data['parentId'] = $parentId;
        $data['title'] = ['en-US' => 'all users in chat: '.$parent->title];
        $data['fullUrl'] = $request->fullUrl().'/mentioning';
        XapiChatService::mentionedAll($parent, $data, $project->id);
    }

    /**
     * @param $request
     * @param $messageId
     * @param $sectionId
     * @param $projectId
     * @return array
     */
    public function createMessageMentionings($request, $messageId, $sectionId, $projectId): array
    {
        $messageMentionings = [];
        foreach ($request->mentionings as $mentionedUser) {
            $mentioning = $this->messageMentioningsRepository->create([
                'user_id' => $mentionedUser,
                'message_id' => $messageId,
                'project_id' => $projectId
            ]);
            array_push($messageMentionings, $mentioning);

            $section = $this->sectionRepository->get($sectionId);
            $project = $this->projectRepository->get($projectId);

            if ($section !== null) {
                $parent = $section;
                $parentId = url('/sectionId/'.$section->id);
            } else {
                $parent = $project;
                $parentId = url('/projectId/'.$project->id);
            }
            $data['parentId'] = $parentId;
            $data['title'] = ['en-US' => 'user in chat: '.$parent->title];
            $data['fullUrl'] = $request->fullUrl().'/mentioning';
            $data['mentionedUser'] = $mentionedUser;
            XapiChatService::mentioned($parent, $data, $project->id);

            broadcast(new MessageMentionEvent($mentioning->id, $messageId, $projectId, $mentionedUser, $parent->title,
                $sectionId))->toOthers();
        }
        return $messageMentionings;
    }

    public function getParticipantsByProject($project)
    {
        $usersWatchIds = array_values($project->user_watch()->pluck('user_id')->toArray());
        $usersInvolveIds = array_values($project->user_involve()->pluck('user_id')->toArray());

        $usersAdminIds = array_values($this->userRepository->getUsersWithRoleAdmin()->pluck('id')->toArray());
        $usersTeamIds = array_values($this->userRepository->getUsersWithRoleTeam()->pluck('id')->toArray());

        $userMessagedInProjectIds = array_values($this->messageRepository->fetchByProjectWithoutSection($project->id)->pluck('user_id')->toArray());

        $mergedUserIds = array_values(array_unique(array_merge(...[$usersWatchIds, $usersInvolveIds, $usersAdminIds, $usersTeamIds, $userMessagedInProjectIds])));
        $users = $this->userRepository->getWhereInWithProfile($mergedUserIds);
        $otherUsers = $this->userRepository->getUsersWhereIdNotInWithProfile($mergedUserIds);

        $users->map(function ($user) {
            $user['showUserInParticipantList'] = true;

            if($user->surname == 'SCoRe-Bot'){
                $user['showUserInParticipantList'] = false;
            }

            return $user;
        });

        return $users->concat($otherUsers);
    }
}
