<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\EP5\VideoSequence;
use App\Models\Media;
use App\User;
use Faker\Generator as Faker;

$factory->define(VideoSequence::class, function (Faker $faker) {
    return [
        'title' => $faker->text,
        'description' => $faker->text,
        'timestamp' => $faker->numberBetween(0, 9000),
        'duration' => $faker->numberBetween(0, 9000),
        'video_nid' => factory(Media::class)->create()->id,
        'user_id' => factory(User::class)->create()->id,
        'created_at' => now(),
        'updated_at' => now()
    ];
});
