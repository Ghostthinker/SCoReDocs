<?php

namespace Tests\Unit\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use App\Rules\Roles;
use App\Rules\Section\SectionCreateValidator;
use App\Rules\Section\SectionDeleteValidator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SectionDeleteValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function testPassesLocked() {
        $section = factory(Section::class)->create(['heading' => 3]);
        $data = ['delete' => 'Test'];

        $user = factory(User::class)->create();
        $this->be($user);

        $v = $this->app['validator']->make($data, ['delete' => new SectionDeleteValidator($section)]);
        $this->assertTrue($v->passes(), 'User with no permission cannot delete section');

        $section->locked = true;
        $v = $this->app['validator']->make($data, ['delete' => new SectionDeleteValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission cannot delete locked section');

        // Give permission
        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_LOCKED);
        $this->assertTrue($v->passes(), 'User with permission can delete locked section');

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::ADMIN);
        $this->be($userTeam);
        $this->assertTrue($v->passes(), 'User with role can delete locked section');
    }

    public function testPassesHeading() {
        $section = factory(Section::class)->create([]);
        $data = ['delete' => 'Test'];

        $user = factory(User::class)->create();
        $this->be($user);

        // check heading
        $section->heading = 3;
        $v = $this->app['validator']->make($data, ['delete' => new SectionDeleteValidator($section)]);
        $this->be($user);
        $this->assertTrue($v->passes(), 'User cannot delete other section with heading > 2');

        $section->author_id = $user->id;
        $this->assertTrue($v->passes(), 'User can delete own section with heading > 2');

        $section->heading = 2;
        $this->assertFalse($v->passes(), 'User without permission cannot delete section with heading < 3');

        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_HEADING_2);
        $this->assertTrue($v->passes(), 'User with permission can delete section with heading < 3');

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::TEAM);
        $this->be($userTeam);
        $this->assertTrue($v->passes(), 'User with role can delete section with heading');
    }

    public function testPassesAdvisor() {
        $section = factory(Section::class)->create(['heading' => 3]);
        $data = ['delete' => 'Test'];

        $user = factory(User::class)->create();
        $this->be($user);

        // check advisor
        $v = $this->app['validator']->make($data, ['delete' => new SectionDeleteValidator($section)]);

        $user =User::find($section->author_id);
        $this->be($user);
        $this->assertTRUE($v->passes(), 'User can delete own section');

        $user = factory(User::class)->create();
        $this->be($user);
        $this->assertTrue($v->passes(), 'User with permission can delete section');

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::TEAM);
        $this->be($userTeam);
        $this->assertTrue($v->passes(), 'User with role can delete section');
    }

    public function testCanDeleteLocked() {
        $section = factory(Section::class)->create([]);
        $user = factory(User::class)->create();
        $this->be($user);

        $section->locked = true;
        $validator = new SectionDeleteValidator($section);
        $this->assertFalse($validator->canDeleteLocked(), 'User without permission');

        $userPerm = factory(User::class)->create();
        $userPerm->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_LOCKED);
        $this->be($userPerm);
        $this->assertTrue($validator->canDeleteLocked(), 'User with permission');

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::ADMIN);
        $this->be($userTeam);
        $this->assertTrue($validator->canDeleteLocked(), 'User as role Team (permission)');
    }

    public function testCanDeleteHeading() {
        $section = factory(Section::class)->create([]);
        $user = factory(User::class)->create();
        $this->be($user);

        $section->heading = 3;
        $validator = new SectionDeleteValidator($section);
        $this->assertTrue($validator->canDeleteHeading(), 'User no need permission by heading > 2');
        $section->heading = 1;
        $this->assertFalse($validator->canDeleteHeading(), 'User without permission');

        $userPerm = factory(User::class)->create();
        $this->be($userPerm);
        $userPerm->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_HEADING_2);
        $this->assertFalse($validator->canDeleteHeading(), 'User with permission for heading 2 cannot delete heading 1');
        $section->heading = 2;
        $this->assertTrue($validator->canDeleteHeading(), 'User with permission for heading 2');

        $userPerm->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_HEADING_1);
        $this->assertTrue($validator->canDeleteHeading(), 'User with permission can heading 1');
        $section->heading = 2;
        $this->assertTrue($validator->canDeleteHeading(), 'User with permission can heading 2');

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::TEAM);
        $this->be($userTeam);
        $this->assertTrue($validator->canDeleteHeading(), 'User as role Team (permission)');
    }
}
