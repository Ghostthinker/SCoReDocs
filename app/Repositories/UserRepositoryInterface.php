<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function all();

    public function get($id);

    public function getFirst($id);

    public function getWithProfile($id);

    public function getAllWithRoles();

    public function getOthersWithProfile($id);

    public function getAllWithProfile();

    public function getUsersWithRoleAdvisor();

    public function getUsersWithRoleStudent();

    public function getUsersWithRoleTeam();

    public function getUsersWithRoleAdmin();

    public function getWhereInWithProfile($ids);

    public function getUsersWhereIdNotInWithProfile($ids);

    public function markIntroVideoAsSeen();

    public function toggleLeftMenuCollapseState();

    public function toggleRightMenuCollapseState();

    /**
     * Method returns the user by assessment id
     *
     * @param $id
     * @return mixed
     */
    public function getUserFromAssessmentDocId($id);
}
