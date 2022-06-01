<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanViewArchive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_VIEW_ARCHIVE]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CAN_VIEW_ARCHIVE
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CAN_VIEW_ARCHIVE
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_VIEW_ARCHIVE)->delete();
    }
}
