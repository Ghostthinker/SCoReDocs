<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Profile;
use App\User;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'avatar' => $faker->text(),
        'course' => $faker->text(),
        'matriculation_number' => $faker->randomNumber(),
        'knowledge' => $faker->text(),
        'personal_resources' => $faker->text(),
        'about_me' => $faker->text(),
        'university' => $faker->text(),
    ];
});
