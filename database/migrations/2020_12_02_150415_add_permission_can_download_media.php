<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanDownloadMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_DOWNLOAD_MEDIA]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CAN_DOWNLOAD_MEDIA
        );

        // Team
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CAN_DOWNLOAD_MEDIA
        );

        // Advisor
        $advisor = Role::findByName(Roles::ADVISOR);
        $advisor->givePermissionTo(
            PermissionSet::CAN_DOWNLOAD_MEDIA
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_DOWNLOAD_MEDIA)->delete();
    }
}
