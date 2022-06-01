<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddRolesAndPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // === ROLES ===

        $admin = Role::create(['name' => Roles::ADMIN]);
        $team = Role::create(['name' => Roles::TEAM]);
        $advisor = Role::create(['name' => Roles::ADVISOR]);
        $student = Role::create(['name' => Roles::STUDENT]);

        // === PERMISSIONS ===

        // User and roles
        Permission::create(['name' => PermissionSet::SET_ROLES]);
        Permission::create(['name' => PermissionSet::GET_ROLES]);
        Permission::create(['name' => PermissionSet::GET_USERS]);

        // Sections
        Permission::create(['name' => PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE]);
        Permission::create(['name' => PermissionSet::SET_STATUS_IN_PROGRESS]);
        Permission::create(['name' => PermissionSet::SET_STATUS_SUBMITTED]);
        Permission::create(['name' => PermissionSet::SET_STATUS_IN_REVIEW]);
        Permission::create(['name' => PermissionSet::SET_STATUS_COMPLETED]);
        Permission::create(['name' => PermissionSet::CHANGE_HEADING_1_CONTENT]);
        Permission::create(['name' => PermissionSet::CHANGE_HEADING_2_CONTENT]);
        Permission::create(['name' => PermissionSet::SET_HEADING_1_TYPE]);
        Permission::create(['name' => PermissionSet::SET_HEADING_2_TYPE]);
        Permission::create(['name' => PermissionSet::CHANGE_HEADING_1_TYPE]);
        Permission::create(['name' => PermissionSet::CHANGE_HEADING_2_TYPE]);
        Permission::create(['name' => PermissionSet::BREAK_SECTION_WORKFLOW]);
        Permission::create(['name' => PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT]);
        Permission::create(['name' => PermissionSet::EDIT_LOCKED_SECTIONS_STATUS]);
        Permission::create(['name' => PermissionSet::SET_LOCKED_SECTIONS_STATUS]);
        Permission::create(['name' => PermissionSet::CHANGE_LOCKED_SECTIONS_HEADING]);

        // Project
        Permission::create(['name' => PermissionSet::EDIT_PROJECTS]);


        // === APPLIANCE ===

        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(Permission::all());

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::EDIT_PROJECTS,
            PermissionSet::CHANGE_HEADING_1_CONTENT,
            PermissionSet::CHANGE_HEADING_2_CONTENT,
            PermissionSet::CHANGE_HEADING_1_TYPE,
            PermissionSet::CHANGE_HEADING_2_TYPE,
            PermissionSet::SET_HEADING_1_TYPE,
            PermissionSet::SET_HEADING_2_TYPE,
            PermissionSet::BREAK_SECTION_WORKFLOW,
            PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT,
            PermissionSet::EDIT_LOCKED_SECTIONS_STATUS,
            PermissionSet::SET_LOCKED_SECTIONS_STATUS,
            PermissionSet::CHANGE_LOCKED_SECTIONS_HEADING,
            PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::SET_STATUS_IN_PROGRESS,
            PermissionSet::SET_STATUS_SUBMITTED,
            PermissionSet::SET_STATUS_IN_REVIEW,
            PermissionSet::SET_STATUS_COMPLETED
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::EDIT_PROJECTS,
            PermissionSet::CHANGE_HEADING_1_CONTENT,
            PermissionSet::CHANGE_HEADING_2_CONTENT,
            PermissionSet::CHANGE_HEADING_1_TYPE,
            PermissionSet::CHANGE_HEADING_2_TYPE,
            PermissionSet::SET_HEADING_1_TYPE,
            PermissionSet::SET_HEADING_2_TYPE,
            PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT,
            PermissionSet::EDIT_LOCKED_SECTIONS_STATUS,
            PermissionSet::CHANGE_LOCKED_SECTIONS_HEADING,
            PermissionSet::SET_LOCKED_SECTIONS_STATUS,
            PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE,
            PermissionSet::SET_STATUS_IN_PROGRESS,
            PermissionSet::SET_STATUS_SUBMITTED,
            PermissionSet::SET_STATUS_IN_REVIEW,
            PermissionSet::SET_STATUS_COMPLETED
        );

        // Student
        $student = Role::findByName(Roles::STUDENT);
        $student->givePermissionTo(
            PermissionSet::SET_STATUS_IN_PROGRESS,
            PermissionSet::SET_STATUS_SUBMITTED
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function down()
    {
        Role::findByName(Roles::ADMIN)->delete();
        Role::findByName(Roles::TEAM)->delete();
        Role::findByName(Roles::ADVISOR)->delete();
        Role::findByName(Roles::STUDENT)->delete();

        // User and roles
        Permission::findByName(PermissionSet::SET_ROLES)->delete();
        Permission::findByName(PermissionSet::GET_ROLES)->delete();
        Permission::findByName(PermissionSet::GET_USERS)->delete();

        // Sections
        Permission::findByName(PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE)->delete();
        Permission::findByName(PermissionSet::SET_STATUS_IN_PROGRESS)->delete();
        Permission::findByName(PermissionSet::SET_STATUS_SUBMITTED)->delete();
        Permission::findByName(PermissionSet::SET_STATUS_IN_REVIEW)->delete();
        Permission::findByName(PermissionSet::SET_STATUS_COMPLETED)->delete();
        Permission::findByName(PermissionSet::BREAK_SECTION_WORKFLOW)->delete();
        Permission::findByName(PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT)->delete();

        // Project
        Permission::findByName(PermissionSet::EDIT_PROJECTS)->delete();
    }
}
