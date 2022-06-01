<?php

namespace App\Services;

use App\Repositories\MessageReadsRepositoryInterface;
use App\Repositories\MessageRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class MessageReadsService
{
    private $messageReadsRepository;
    private $messageRepository;

    public function __construct(MessageReadsRepositoryInterface $messageReadsRepository, MessageRepositoryInterface $messageRepository)
    {
        $this->messageReadsRepository = $messageReadsRepository;
        $this->messageRepository = $messageRepository;
    }

    // mark messages as read and unread

    /**
     * @param $userId
     * @param $projectId
     * @param $sectionId
     */
    public function markMessagesAsRead($projectId, $sectionId) {
        if ($sectionId !== 'null') {
            $messages = $this->messageRepository->fetchForSection($projectId, $sectionId);
            $readMessagesForSectionByUser = $this->messageReadsRepository->fetchBySectionIdForUser($sectionId, Auth::id());
            $this->createReadMessages($sectionId, $projectId, $messages, $readMessagesForSectionByUser);
        } else {
            $messages = $this->messageRepository->fetchByProject($projectId);
            $readMessagesForProjectByUser = $this->messageReadsRepository->fetchByProjectIdForUser($projectId, Auth::id());
            $this->createReadMessages(null, $projectId, $messages, $readMessagesForProjectByUser);
        }
        return;
    }

    /**
     * @param $userId
     * @param $messageId
     * @param $sectionId
     * @param $projectId
     */
    public function markMessageAsRead($messageId, $sectionId, $projectId) {
        $this->messageReadsRepository->create([
            'user_id' => Auth::id(),
            'message_id' => $messageId,
            'section_id' => $sectionId,
            'project_id' => $projectId
        ]);
    }

    public function markMessageAsUnread($userId, $messageId) {
        //tbi
    }

    /**
     * @param $sectionId
     * @param $projectId
     * @param $messages
     * @param $readMessages
     */
    private function createReadMessages($sectionId, $projectId, $messages, $readMessages) {
        $messageCount = count($messages);
        if($messageCount > count($readMessages)) {
            foreach ($messages as $message) {
                if(!$readMessages->contains('messageId', $message->id)) {
                    $this->messageReadsRepository->create([
                        'user_id' => Auth::id(),
                        'message_id' => $message->id,
                        'section_id' => $sectionId,
                        'project_id' => $projectId
                    ]);
                }
            }
        }
    }

}
