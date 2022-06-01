<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddCanDeleteNewsPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::DELETE_NEWS]);

        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::DELETE_NEWS
        );

        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::DELETE_NEWS
        );

        $team = Role::findByName(Roles::TEAMUB);
        $team->givePermissionTo(
            PermissionSet::DELETE_NEWS
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $admin = Role::findByName(Roles::ADMIN);
        $admin->revokePermissionTo(
            PermissionSet::DELETE_NEWS
        );

        $team = Role::findByName(Roles::TEAM);
        $team->revokePermissionTo(
            PermissionSet::DELETE_NEWS
        );

        $team = Role::findByName(Roles::TEAMUB);
        $team->revokePermissionTo(
            PermissionSet::DELETE_NEWS
        );

        Permission::findByName(PermissionSet::DELETE_NEWS)->delete();
    }
}
