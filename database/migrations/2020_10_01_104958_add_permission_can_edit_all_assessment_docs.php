<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanEditAllAssessmentDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::EDIT_ALL_ASSESSMENT_DOCS]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::EDIT_ALL_ASSESSMENT_DOCS
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::EDIT_ALL_ASSESSMENT_DOCS
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::EDIT_ALL_ASSESSMENT_DOCS
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::EDIT_ALL_ASSESSMENT_DOCS)->delete();
    }
}
