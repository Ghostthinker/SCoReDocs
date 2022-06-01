<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Message;
use App\Models\Project;
use App\User;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'type' => $faker->text(190),
        'data' => [],
        'user_id' => factory(User::class),
        'project' => factory(Project::class)
    ];
});

$factory->state(Message::class, 'text', function ($faker) {
    return [
        'type' => $faker->text(190),
        'data' => [
            'text' => "A text"
        ],
        'user_id' => factory(User::class),
        'project' => factory(Project::class)
    ];
});
