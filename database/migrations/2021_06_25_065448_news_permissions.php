<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => PermissionSet::CREATE_NEWS]);
        Permission::create(['name' => PermissionSet::EDIT_NEWS]);
        // Admin
        $admin = Role::findByName(Roles::ADMIN);
        $admin->givePermissionTo(
            PermissionSet::CREATE_NEWS
        );
        $admin->givePermissionTo(
            PermissionSet::EDIT_NEWS
        );

        // Team
        $team = Role::findByName(Roles::TEAMUB);
        $team->givePermissionTo(
            PermissionSet::CREATE_NEWS
        );
        $team->givePermissionTo(
            PermissionSet::EDIT_NEWS
        );
        $team = Role::findByName(Roles::TEAM);
        $team->givePermissionTo(
            PermissionSet::CREATE_NEWS
        );
        $team->givePermissionTo(
            PermissionSet::EDIT_NEWS
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::findByName(PermissionSet::CREATE_NEWS)->delete();
        Permission::findByName(PermissionSet::EDIT_NEWS)->delete();
    }
}
