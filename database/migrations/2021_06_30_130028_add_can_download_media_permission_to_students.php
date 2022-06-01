<?php

use App\Rules\PermissionSet;
use App\Rules\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class AddCanDownloadMediaPermissionToStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $student = Role::findByName(Roles::STUDENT);
        $student->givePermissionTo(
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
        $student = Role::findByName(Roles::STUDENT);
        $student->revokePermissionTo(
            PermissionSet::CAN_DOWNLOAD_MEDIA
        );
    }
}
