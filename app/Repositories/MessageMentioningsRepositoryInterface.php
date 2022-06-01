<?php

namespace App\Repositories;

interface MessageMentioningsRepositoryInterface
{
    /**
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function getWithMessageAndProjectAndSectionByProjectAndUser($projectId, $userId);

    /**
     * @param $mentioningId
     * @return mixed
     */
    public function getWithMessage($mentioningId);

    /**
     * @param $messageMentioning
     * @return mixed
     */
    public function markAsRead($messageMentioning);
}
