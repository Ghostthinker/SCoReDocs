<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Events\MessageCountEvent;
use App\Events\MessageEvent;
use App\Http\Resources\MessageMentioningResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageResourceCollection;
use App\Http\Resources\ParticipantsResource;
use App\Models\Project;
use App\Repositories\MessageMentioningsRepositoryInterface;
use App\Repositories\MessageReadsRepositoryInterface;
use App\Repositories\MessageRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\SectionRepositoryInterface;
use App\Services\ActivityService;
use App\Services\MessageReadsService;
use App\Services\MessageService;
use App\Services\Xapi\XapiChatService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatController extends Controller
{
    /**
     * Creatings a new message which is then broadcasted to other Clients
     *
     * @param  Request  $request
     * @param  MessageRepositoryInterface  $repository
     * @param  int  $projectId  ProjectId for the message
     * @param  int|null  $sectionId  SectionId for the message
     *
     * @return mixed
     */
    public function sendMessage(
        Request $request,
        MessageRepositoryInterface $repository,
        MessageReadsService $messageReadsService,
        ActivityService $activityService,
        MessageService $messageService,
        int $projectId,
        int $sectionId = null
    ) {
        $user = Auth::user();
        $message = $repository->create([
            'user_id' => $user->id,
            'project' => $projectId,
            'section_id' => $sectionId,
            'type' => $request->type,
            'data' => $request->data,
            'parent_id' => $request->parent_id,
            'at_all_mentioning' => $request->atAllMentioning
        ]);
        $parsedMessage = MessageResource::make($message);

        if ($request->atAllMentioning) {
            $messageService->createAtAllMentioning($request, $message->id, $sectionId, $projectId);
            $activityService->createAndBroadcastActivity(Auth::id(),
                $message->data['text'], $sectionId, $projectId, ActivityType::ATALL);
        } else {
            $messageService->createMessageMentionings($request, $message->id, $sectionId, $projectId);
        }
        $matches = [];
        $userRegex = "/\[\[user:(\d+)]]/";
        if (preg_match_all($userRegex, $message->data['text'], $matches)) {
            $activityService->createAndBroadcastActivityMentioning(Auth::id(),
                $message->data['text'], $sectionId, $projectId, $message->id);
        }

        $messageReadsService->markMessageAsRead($message->id, $sectionId, $projectId);

        broadcast(new MessageEvent($parsedMessage, $projectId, $sectionId))->toOthers();
        broadcast(new MessageCountEvent($projectId, $message->id, $sectionId))->toOthers();
        return $parsedMessage;
    }

    /**
     * Returns messages for a project
     *
     * @param  Request  $request
     * @param  MessageRepositoryInterface  $repository
     * @param  MessageReadsService  $messageReadsService
     * @param  int  $projectId  projectId of the messages
     *
     * @return MessageResourceCollection
     */
    public function getMessagesForProject(
        Request $request,
        MessageRepositoryInterface $repository,
        MessageReadsService $messageReadsService,
        $projectId
    ) {
        $messages = $repository->fetchByProject($projectId);
        return MessageResourceCollection::make($messages)->actualUser(Auth::id());
    }

    /**
     * Returns messages for a context
     *
     * @param  Request  $request
     * @param  MessageRepositoryInterface  $repository
     * @param  int  $projectId  projectId of the messages
     * @param  int|null  $sectionId  sectionId of the messages
     *
     * @return MessageResourceCollection
     */
    public function getMessagesForContext(
        Request $request,
        MessageRepositoryInterface $repository,
        $projectId,
        $sectionId = null
    ) {
        $messages = $repository->fetchForSection($projectId, $sectionId);
        return MessageResourceCollection::make($messages)->actualUser(Auth::id());
    }

    /**
     * Returns all Users as Participants
     *
     * @param  Request  $request
     * @param  Project  $project
     * @param  MessageService  $messageService
     * @return AnonymousResourceCollection
     */
    public function getParticipants(Request $request, Project $project, MessageService $messageService): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = $messageService->getParticipantsByProject($project);
        return ParticipantsResource::collection($users);
    }

    /**
     * @param  Request  $request
     * @param  MessageReadsRepositoryInterface  $messageReadsRepository
     * @param $projectId
     * @return mixed
     */
    public function getUnreadMessageCounts(
        Request $request,
        MessageReadsRepositoryInterface $messageReadsRepository,
        $projectId
    ) {
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($projectId, Auth::id());
        $data = $project->sections->map(function ($item) use ($projectId) {
            return [
                'sectionId' => $item->id,
                'projectId' => $projectId,
                'unreadMessageCount' => $item->messages->count(),
                'userWasInvolvedInSection' => $item->audits_count > 0,
                'firstUnreadMessageId' => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        })->toArray();
        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($projectId, Auth::id());
        $firstUnreadProjectMessage = $messageReadsRepository->getFirstUnreadProjectMessageByProjectAndUser($projectId, Auth::id());
        array_push($data, [
            'sectionId' => null,
            'projectId' => $projectId,
            'unreadMessageCount' => $projectMessageCount,
            'firstUnreadMessageId' => $firstUnreadProjectMessage ? $firstUnreadProjectMessage->id : null
        ]);
        return $data;
    }

    /**
     * @param  Request  $request
     * @param  MessageReadsService  $messageReadsService
     * @param $projectId
     * @param $sectionid
     */
    public function updateReadMessages(
        Request $request,
        MessageReadsService $messageReadsService,
        $projectId,
        $sectionid
    ) {
        $messageReadsService->markMessagesAsRead($projectId, $sectionid);
    }

    /**
     * @param  Request  $request
     * @param $projectId
     * @param  MessageMentioningsRepositoryInterface  $messageMentioningsRepository
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getMessageMentioningsForProject(
        Request $request,
        $projectId,
        MessageMentioningsRepositoryInterface $messageMentioningsRepository
    ) {
        $mentionings = $messageMentioningsRepository->getWithMessageAndProjectAndSectionByProjectAndUser($projectId,
            Auth::id());
        return MessageMentioningResource::collection($mentionings);
    }

    /**
     * @param  Request  $request
     * @param $messageMentioningId
     * @param  MessageMentioningsRepositoryInterface  $messageMentioningsRepository
     * @param  SectionRepositoryInterface  $sectionRepository
     * @param  ProjectRepositoryInterface  $projectRepository
     * @return mixed
     */
    public function markMessageMentioningAsRead(
        Request $request,
        $messageMentioningId,
        MessageMentioningsRepositoryInterface $messageMentioningsRepository,
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $messageMentioning = $messageMentioningsRepository->getWithMessage($messageMentioningId);
        $success = $messageMentioningsRepository->markAsRead($messageMentioning);
        if ($success) {
            $section = $sectionRepository->get($messageMentioning->message->section_id);
            $project = $projectRepository->get($messageMentioning->project_id);
            if ($section !== null) {
                $parent = $section;
                $parentId = url('/sectionId/'.$section->id);
            } else {
                $parent = $project;
                $parentId = url('/projectId/'.$project->id);
            }
            $data = [];
            $data['parentId'] = $parentId;
            $data['title'] = ['en-US' => 'mentioning in chat: '.$parent->title];
            $data['fullUrl'] = $request->fullUrl().'/mentioning';
            $data['messageId'] = $messageMentioning->message_id;

            XapiChatService::openedMentioning($parent, $data, $project->id);
        }
        return $success;
    }

    public function markAllSectionMessagesAsRead($projectId, MessageReadsRepositoryInterface $messageReadsRepository)
    {
        return $messageReadsRepository->markAllSectionMessagesAsRead($projectId, Auth::user()->id);
    }
}
