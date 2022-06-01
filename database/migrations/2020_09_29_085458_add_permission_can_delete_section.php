<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanDeleteSection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_DELETE_SECTIONS_HEADING_1]);
        Permission::create(['name' => PermissionSet::CAN_DELETE_SECTIONS_HEADING_2]);
        Permission::create(['name' => PermissionSet::CAN_DELETE_SECTIONS_LOCKED]);
        Permission::create(['name' => PermissionSet::CAN_DELETE_SECTIONS_ADVISOR]);

        $roles = [Roles::ADMIN, Roles::TEAM, Roles::ADVISOR];

        foreach ($roles as $roleName) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo(
                PermissionSet::CAN_DELETE_SECTIONS_HEADING_1,
                PermissionSet::CAN_DELETE_SECTIONS_HEADING_2,
                PermissionSet::CAN_DELETE_SECTIONS_ADVISOR
            );
        }

        $role = Role::findByName(Roles::ADMIN);
        $role->givePermissionTo(
            PermissionSet::CAN_DELETE_SECTIONS_LOCKED
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_DELETE_SECTIONS_HEADING_1)->delete();
        Permission::findByName(PermissionSet::CAN_DELETE_SECTIONS_HEADING_2)->delete();
        Permission::findByName(PermissionSet::CAN_DELETE_SECTIONS_LOCKED)->delete();
        Permission::findByName(PermissionSet::CAN_DELETE_SECTIONS_ADVISOR)->delete();
    }
}
