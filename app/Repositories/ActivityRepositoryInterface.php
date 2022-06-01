<?php


namespace App\Repositories;


interface ActivityRepositoryInterface
{
    /**
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data);

    public function getWithSectionAndProject($id);

    /**
     * @param $projectIds
     * @return mixed
     */
    public function fetchByProjectIds($projectIds);

    /**
     * @param $projectIds
     * @return mixed
     */
    public function fetchByProjectIdsWithMentionings($projectIds);

    /**
     * @param $projectId
     * @return mixed
     */
    public function paginationByProjectId($projectId);

    /**
     * @param $projectId
     * @param $sectionId
     * @return mixed
     */
    public function paginationByProjectIdBySectionId($projectId, $sectionId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function getCountOfUnreadActivitiesByProjectAndUser($projectId, $userId);

    /**
     * @param $projectId
     * @param $userId
     * @return mixed
     */
    public function markAllByProjectAsRead($projectId, $userId);
}
