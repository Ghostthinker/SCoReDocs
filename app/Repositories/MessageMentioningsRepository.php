<?php

namespace App\Repositories;

use App\Models\MessageMentionings;

class MessageMentioningsRepository implements MessageMentioningsRepositoryInterface
{
    public function create(array $data) {
        $messageMentioning = MessageMentionings::firstOrCreate($data);
        if($messageMentioning->save()) {
            return $messageMentioning;
        }
        return null;
    }

    public function getWithMessageAndProjectAndSectionByProjectAndUser($projectId, $userId)
    {
        return MessageMentionings::where('project_id', $projectId)->where('user_id', $userId)->whereNull('read_at')->with('message', 'message.section', 'project')->get();
    }

    public function getWithMessage($mentioningId)
    {
        return  MessageMentionings::where('id', $mentioningId)->with('message')->first();
    }


    public function markAsRead($messageMentioning)
    {
        $messageMentioning->read_at = now();
        return $messageMentioning->save();
    }
}
