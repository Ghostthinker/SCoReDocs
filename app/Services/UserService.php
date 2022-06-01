<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use Spatie\Permission\Models\Role;

class UserService
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * Setting roles for an array of user - role tuples
     *
     * @param $data
     */
    public function updateRoles($data)
    {
        foreach ($data as $userRole) {
            $role = Role::findById($userRole['roleId']);
            $user = $this->userRepository->get($userRole['userId']);
            $user->syncRoles([$role->name]);
        }
    }
}
