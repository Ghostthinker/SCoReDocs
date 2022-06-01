<?php


namespace App\Repositories;


interface DailyDigestRepositoryInterface
{

    public function getNewCreatedSectionAmount($project, $startDate, $endDate);

    public function getChangedSectionsAmount($project, $startDate, $endDate);

    public function getNewCreatedVideoAmount($project, $startDate, $endDate);

    public function getNewCreatedAnnotationAmount($project, $startDate, $endDate);

    public function getChangedSectionAmount($section, $startDate, $endDate): int;

    /**
     * Get amount of mentioning by user
     *
     * @param $userId
     * @param $from
     * @param $to
     * @return mixed
     */
    public function getAmountOfProjectsUserWasMentionedFromTo($userId, $from, $to);

    public function getAtAllMentioningOfProjectByUserId($project, $userId);

}
