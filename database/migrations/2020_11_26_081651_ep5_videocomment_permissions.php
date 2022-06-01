<?php

use Illuminate\Database\Migrations\Migration;
use App\Rules\PermissionSet;
use App\Rules\Roles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Ep5VideocommentPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::UPDATE_OWN_VIDEOCOMMENT]);
        Permission::create(['name' => PermissionSet::UPDATE_ANY_VIDEOCOMMENT]);
        Permission::create(['name' => PermissionSet::DELETE_OWN_VIDEOCOMMENT]);
        Permission::create(['name' => PermissionSet::DELETE_ANY_VIDEOCOMMENT]);
        Permission::create(['name' => PermissionSet::REPLY_TO_VIDEOCOMMENT]);

        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::UPDATE_OWN_VIDEOCOMMENT,
            PermissionSet::UPDATE_ANY_VIDEOCOMMENT,
            PermissionSet::DELETE_OWN_VIDEOCOMMENT,
            PermissionSet::DELETE_ANY_VIDEOCOMMENT,
            PermissionSet::REPLY_TO_VIDEOCOMMENT
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::UPDATE_OWN_VIDEOCOMMENT,
            PermissionSet::DELETE_OWN_VIDEOCOMMENT,
            PermissionSet::REPLY_TO_VIDEOCOMMENT
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::UPDATE_OWN_VIDEOCOMMENT,
            PermissionSet::DELETE_OWN_VIDEOCOMMENT,
            PermissionSet::REPLY_TO_VIDEOCOMMENT
        );

        // Student
        $student = Role::findByName(Roles::STUDENT);
        $student->givePermissionTo(
            PermissionSet::UPDATE_OWN_VIDEOCOMMENT,
            PermissionSet::DELETE_OWN_VIDEOCOMMENT,
            PermissionSet::REPLY_TO_VIDEOCOMMENT
        );
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
