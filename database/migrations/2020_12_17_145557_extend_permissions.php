<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExtendPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW]);
        Permission::create(['name' => PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED]);
        Permission::create(['name' => PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE]);
        Permission::create(['name' => PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW]);
        Permission::create(['name' => PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW,
            PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW)->delete();
        Permission::findByName(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED)->delete();
        Permission::findByName(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE)->delete();
        Permission::findByName(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW)->delete();
        Permission::findByName(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED)->delete();
    }
}
