<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @param array $data
     *
     * @return Message|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $section = Message::make($data);
        if ($section->save()) {
            return $section;
        }
        return null;
    }

    /**
     * @param $id
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchByProject($id)
    {
        return Message::where('project', '=', $id)->with('section')->get();
    }

    /**
     * @param $id
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchByProjectWithoutSection($id)
    {
        return Message::where('project', '=', $id)->get();
    }

    /**
     * @param $projectId
     * @param $sectionId
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchForSection($projectId, $sectionId)
    {
        return Message::where('project', '=', $projectId)->where('section_id', '=', $sectionId)->get();
    }

    /**
     * @param $userId
     * @param $projectId
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchByProjectForUser($userId, $projectId) {
        return Message::where('project', '=', $projectId)->where('user_id', '=', $userId)->get();
    }

    /**
     * @param $userId
     * @param $projectId
     * @param $sectionId
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchBySectionForUser($userId, $projectId, $sectionId) {
        return Message::where('project', '=', $projectId)
            ->where('section_id', '=', $sectionId)
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function fetchOnlyProjectChatMessages($projectId) {
        return Message::where('project', '=', $projectId)
            ->where('section_id', '=', null)
            ->get();
    }

    public function get($id)
    {
        return Message::find($id);
    }
}
