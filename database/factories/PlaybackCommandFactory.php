<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EP5\PlaybackCommand;
use Faker\Generator as Faker;

$factory->define(PlaybackCommand::class, function (Faker $faker) {
    return [
        'duration' => $faker->randomNumber(),
        'timestamp' => $faker->randomNumber(),
        'title' => $faker->text(),
        'type' => $faker->text(),
        'additional_fields' => [],
        'video_nid' => factory(\App\Models\Media::class),
        'user_id' => factory(\App\User::class),
    ];
});

$factory->state(PlaybackCommand::class, 'zoom', function ($faker) {
    return [
        'type' => 'zoom',
        'additional_fields' => [
            'zoom' => "4.5",
            'zoom_transform' => ["4.5", "0", "0", "4.5", 0.31690140845070425, 0.4126760563380282]
        ],
    ];
});
