<?php

namespace Tests\Http\Controllers;

use App\Rules\Roles;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetUsers()
    {
        $user = factory(User::class)->create();
        $sectionResponse = $this->actingAs($user)->get("rest/users");
        $sectionResponse->assertStatus(403);
        $user->assignRole('Admin');
        $sectionResponse = $this->actingAs($user)->get("rest/users");
        $sectionResponse->assertStatus(200);
        $this->assertTrue(count($sectionResponse->getOriginalContent()->toArray()) == 1);
    }

    public function testSetRole()
    {
        $admin = factory(User::class)->create();
        $admin->assignRole('Admin');
        $user = factory(User::class)->create();
        $sectionResponse = $this->actingAs($admin)->patch("rest/users/roles", [[
            'userId' => $user->id,
            'roleId' => 2
        ]]);
        $sectionResponse->assertStatus(200);
        $sectionResponse = $this->actingAs($user)->patch("rest/users/roles", [[
            'userId' => $user->id,
            'roleId' => 2
        ]]);
        $sectionResponse->assertStatus(403);
    }

    public function testRoles()
    {
        $admin = factory(User::class)->create();
        $admin->assignRole('Admin');
        $sectionResponse = $this->actingAs($admin)->get("rest/users/roles");
        $sectionResponse->assertStatus(200);
        $user = factory(User::class)->create();
        $sectionResponse = $this->actingAs($user)->get("rest/users/roles");
        $sectionResponse->assertStatus(403);
    }
}
