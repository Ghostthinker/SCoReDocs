<?php

namespace App\Repositories;

use App\Models\Message;

interface MessageRepositoryInterface
{
    /**
     * @param array $data
     *
     * @return Message|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * @param $id
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchByProject($id);

    /**
     * @param $id
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchByProjectWithoutSection($id);

    /**
     * @param $projectId
     * @param $sectionId
     *
     * @return Message[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchForSection($projectId, $sectionId);

    /**
     * @param $userId
     * @param $projectId
     *
     * @return mixed
     */
    public function fetchByProjectForUser($userId, $projectId);

    /**
     * @param $userId
     * @param $projectId
     * @param $sectionId
     *
     * @return mixed
     */
    public function fetchBySectionForUser($userId, $projectId, $sectionId);


    /**
     * @param $projectId
     * @return mixed
     */
    public function fetchOnlyProjectChatMessages($projectId);

}
