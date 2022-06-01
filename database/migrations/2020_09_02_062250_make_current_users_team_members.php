<?php

use App\Models\Profile;
use App\Rules\Roles;
use App\User;
use Illuminate\Database\Migrations\Migration;

class MakeCurrentUsersTeamMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $users = User::withTrashed();
        foreach ($users as $user) {
            if (!$user->hasAnyRole([Roles::ADMIN, Roles::TEAM, Roles::ADVISOR, Roles::STUDENT])) {
                $user->assignRole(Roles::TEAM);
            }
            $profile = Profile::where('user_id', $user->id)->first();
            if (!$profile) {
                if ($user->id) {
                    Profile::create([
                        'user_id' => $user->id
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
