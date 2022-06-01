<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanAddSectionToAssessment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT)->delete();
    }
}
