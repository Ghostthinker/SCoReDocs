<?php

namespace Tests\Unit\Services;

use App\Repositories\UserRepository;
use App\Services\UserService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{

    use RefreshDatabase;

    public function testUpdateRole()
    {
        $repo = $this->app->make(UserRepository::class);
        $userService = new UserService($repo);
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $userService->updateRoles([[
            'userId' => $user->id,
            'roleId' => 2
        ], [
            'userId' => $user2->id,
            'roleId' => 3
        ]]);
        $user = User::with('roles')->where('id', $user->id)->get()->first();
        $this->assertEquals(2, $user->roles->first()->id);
        $user2 = User::with('roles')->where('id', $user2->id)->get()->first();
        $this->assertEquals(3, $user2->roles->first()->id);
    }
}
