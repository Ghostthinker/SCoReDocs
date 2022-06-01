<?php

namespace App\Repositories;

interface MessageReadsRepositoryInterface
{
    /**
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $userId
     * @return mixed
     */
    public function fetchByUserId($userId);

    /**
     * @param $projectId
     * @return mixed
     */
    public function fetchByProjectId($projectId);

    /**
     * @param $sectionId
     * @return mixed
     */
    public function fetchBySectionId($sectionId);

    /**
     * @param $messageId
     * @return mixed
     */
    public function fetchByMessageId($messageId);

    /**
     * @param $sectionId
     * @param $userId
     * @return mixed
     */
    public function fetchBySectionIdForUser($sectionId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function fetchByProjectIdForUser($projectId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function markAllSectionMessagesAsRead($projectId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function getCountOfUnreadSectionMessagesByProjectAndUser($projectId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function getCountOfUnreadProjectMessagesByProjectAndUser($projectId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function getFirstUnreadProjectMessageByProjectAndUser($projectId, $userId);
}
