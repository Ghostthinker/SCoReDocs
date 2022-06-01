<?php

namespace App\Repositories;

use App\Rules\Roles;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::all();
    }

    public function get($id)
    {
        return User::findOrFail($id);
    }

    public function getFirst($id)
    {
        return User::whereId($id)->firstOrFail();
    }

    public function getWithProfile($id)
    {
        return User::whereId($id)->with('profile')->firstOrFail();
    }

    public function getAllWithRoles()
    {
        return User::with('roles')->where('surname', '!=', 'SCoRe-Bot')->get();
    }

    public function getOthersWithProfile($id)
    {
        return User::where('id', '!=', $id)->with('profile')->get();
    }

    public function getAllWithProfile()
    {
        return User::withTrashed()->with('profile')->get();
    }

    public function getUsersWithRoleAdvisor()
    {
        $users = $this->getAllWithRoles();
        return $users->reject(function ($user, $key) {
            if ($user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::STUDENT) ||
                $user->hasRole(Roles::TEAM)) {
                return $user;
            }
        });
    }

    public function getUsersWithRoleStudent()
    {
        return User::role(Roles::STUDENT)->get();
    }

    public function getUsersWithRoleTeam()
    {
        $usersTeam = User::role(Roles::TEAM)->get();
        $userTeamUB = User::role(Roles::TEAMUB)->get();
        return $usersTeam->merge($userTeamUB);
    }

    public function getUsersWithRoleAdmin()
    {
        return User::role(Roles::ADMIN)->get();
    }

    public function getWhereInWithProfile($ids)
    {
        return User::whereIn('id', $ids)->with('profile')->get();
    }

    public function getUsersWhereIdNotInWithProfile($ids) {
        return User::whereNotIn('id', $ids)->with('profile')->get();
    }

    public function getUserFromAssessmentDocId($id)
    {
        return User::where('assessment_doc_id', $id)->firstOrFail();
    }

    public function markIntroVideoAsSeen()
    {
        return Auth::user()->update(['has_seen_intro_video' => 1]);
    }

    public function toggleLeftMenuCollapseState() {
        return Auth::user()->update(['left_menu_collapsed' => !Auth::user()->getAttribute('left_menu_collapsed')]);
    }

    public function toggleRightMenuCollapseState() {
        return Auth::user()->update(['right_menu_collapsed' => !Auth::user()->getAttribute('right_menu_collapsed')]);
    }
}
