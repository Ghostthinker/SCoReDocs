<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use App\Models\EP5\Annotation;
use Faker\Generator as Faker;

$factory->define(Annotation::class, function (Faker $faker) {
    return [
        'body' => $faker->text,
        'drawing_data' => $faker->text,
        'timestamp' => $faker->numberBetween(0, 9000),
        'video_nid' => factory(\App\Models\Media::class)->create()->id,
        'created_at' => now(),
        'updated_at' => now(),
        'user_id' => Auth::id()
    ];
});
