<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionCanDownloadAgreementsDataProcessing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING
        );

        // Team
        $team = Role::findByName(Roles::TEAMUB);
        $team->givePermissionTo(
            PermissionSet::CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING)->delete();
    }
}
