<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanExportData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_EXPORT_DATA]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CAN_EXPORT_DATA
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CAN_EXPORT_DATA
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_EXPORT_DATA)->delete();
    }
}
