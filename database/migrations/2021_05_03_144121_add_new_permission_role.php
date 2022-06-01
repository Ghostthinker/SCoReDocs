<?php

use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddNewPermissionRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create([ 'name' => 'SCoRe-Team-UB' ]);
        $moderator = Role::findByName(Roles::TEAMUB);
        $moderator->givePermissionTo(
            Role::findByName('SCoRe-Team')->permissions
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::findByName(Roles::TEAMUB)->delete();
    }
}
