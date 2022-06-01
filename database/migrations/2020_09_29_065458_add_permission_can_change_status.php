<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanChangeStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE]);
        Permission::create(['name' => PermissionSet::CHANGE_STATUS_IN_PROGRESS]);
        Permission::create(['name' => PermissionSet::CHANGE_STATUS_SUBMITTED]);
        Permission::create(['name' => PermissionSet::CHANGE_STATUS_IN_REVIEW]);
        Permission::create(['name' => PermissionSet::CHANGE_STATUS_COMPLETED]);

        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::CHANGE_STATUS_IN_PROGRESS,
            PermissionSet::CHANGE_STATUS_SUBMITTED,
            PermissionSet::CHANGE_STATUS_IN_REVIEW,
            PermissionSet::CHANGE_STATUS_COMPLETED
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::CHANGE_STATUS_IN_PROGRESS,
            PermissionSet::CHANGE_STATUS_SUBMITTED,
            PermissionSet::CHANGE_STATUS_IN_REVIEW,
            PermissionSet::CHANGE_STATUS_COMPLETED
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::CHANGE_STATUS_IN_PROGRESS,
            PermissionSet::CHANGE_STATUS_SUBMITTED,
            PermissionSet::CHANGE_STATUS_IN_REVIEW,
            PermissionSet::CHANGE_STATUS_COMPLETED
        );

        // Student
        $student = Role::findByName(Roles::STUDENT);
        $student->givePermissionTo(
            PermissionSet::CHANGE_STATUS_IN_PROGRESS,
            PermissionSet::CHANGE_STATUS_SUBMITTED
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE)->delete();
        Permission::findByName(PermissionSet::CHANGE_STATUS_IN_PROGRESS)->delete();
        Permission::findByName(PermissionSet::CHANGE_STATUS_SUBMITTED)->delete();
        Permission::findByName(PermissionSet::CHANGE_STATUS_IN_REVIEW)->delete();
        Permission::findByName(PermissionSet::CHANGE_STATUS_COMPLETED)->delete();
    }
}
