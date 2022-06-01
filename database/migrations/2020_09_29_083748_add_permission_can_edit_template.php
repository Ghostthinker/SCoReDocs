<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanEditTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::EDIT_TEMPLATES]);

        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::EDIT_TEMPLATES
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::EDIT_TEMPLATES
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::EDIT_TEMPLATES
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::EDIT_TEMPLATES)->delete();
    }
}
