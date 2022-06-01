<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\MessageReads;
use App\Models\Project;

class MessageReadsRepository implements MessageReadsRepositoryInterface
{
    public function create(array $data)
    {
        $messageRead = MessageReads::firstOrCreate($data);
        if ($messageRead->save()) {
            return $messageRead;
        }
        return null;
    }

    public function delete($messageId)
    {
        MessageReads::where('messageId', $messageId)->delete();
    }

    public function fetchByUserId($userId)
    {
        return MessageReads::where('user_id', $userId)->get();
    }

    public function fetchByProjectId($projectId)
    {
        return MessageReads::where('project_id', $projectId)->get();
    }

    public function fetchBySectionId($sectionId)
    {
        return MessageReads::where('section_id', $sectionId)->get();
    }

    public function fetchByMessageId($MessageId)
    {
        return MessageReads::where('message_id', $MessageId)->get();
    }

    public function fetchBySectionIdForUser($sectionId, $userId)
    {
        return MessageReads::where([
            'section_id' => $sectionId,
            'user_id' => $userId
        ])->get();
    }

    public function fetchByProjectIdForUser($projectId, $userId)
    {
        return MessageReads::where([
            'project_id' => $projectId,
            'section_id' => null,
            'user_id' => $userId
        ])->get();
    }

    public function markAllSectionMessagesAsRead($projectId, $userId)
    {
        $messages = Message::where('project', $projectId)->whereDoesntHave('message_reads', function ($q) use($userId) {
            $q->where('user_id', $userId);
            $q->whereNotNull('section_id');
        })->whereNotNull('section_id')->get();
        $messageReadsData = array();
        foreach ($messages as $message) {
            $messageReadsData[] = [
                'user_id' => $userId,
                'message_id' => $message->id,
                'section_id' => $message->section_id,
                'project_id' => $message->project
            ];
        }
        return MessageReads::insert($messageReadsData);
    }

    public function getCountOfUnreadSectionMessagesByProjectAndUser($projectId, $userId)
    {
        return Project::where('id', $projectId)->with([
            'sections' => function ($q) use ($userId) {
                $q->with([
                    'messages' => function ($q) use ($userId) {
                        $q->whereDoesntHave('message_reads', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                            $q->whereNotNull('section_id');
                        })->whereNotNull('section_id')->orderBy('id', 'asc');
                    }
                ]);
                $q->withCount([
                    'audits' => function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }
                ]);
            }
        ])->first();
    }

    public function getCountOfUnreadProjectMessagesByProjectAndUser($projectId, $userId)
    {
        return Message::where('project', $projectId)
            ->whereNull('section_id')
            ->whereDoesntHave('message_reads', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
            })->count();
    }

    public function getFirstUnreadProjectMessageByProjectAndUser($projectId, $userId)
    {
        return Message::where('project', $projectId)
            ->whereNull('section_id')
            ->whereDoesntHave('message_reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->orderBy('id', 'asc')->first();
    }
}
