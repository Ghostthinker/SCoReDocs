<?php

use App\Rules\Roles;
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's make sure everyone has the same password and
        // let's hash it before the loop, or else our seeder
        // will be too slow.
        $password = Hash::make('admin');

        $admin = User::create([
            'firstName' => 'Administrator',
            'surName' => 'empty',
            'email' => 'admin@score.de',
            'password' => $password,
        ]);
        $admin->assignRole(Roles::ADMIN);
        $user = User::create([
            'firstname' => 'User',
            'surName' => 'empty',
            'email' => 'user@score.de',
            'password' => $password,
        ]);
        $user->assignRole(Roles::STUDENT);
        $advisor = User::create([
            'firstname' => 'Advisor',
            'surName' => 'empty',
            'email' => 'advisor@score.de',
            'password' => $password,
        ]);
        $advisor->assignRole(Roles::ADVISOR);
        $team = User::create([
            'firstname' => 'Team',
            'surName' => 'empty',
            'email' => 'team@score.de',
            'password' => $password,
        ]);
        $team->assignRole(Roles::TEAM);
    }

}
