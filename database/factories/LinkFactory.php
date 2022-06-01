<?php

use App\Models\Link;
use App\Models\Section;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Link::class, function (Faker $faker) {
    return [
        'ref_id' => $faker->uuid,
        'target' => $faker->url,
        'origin' => null,
        'section_id' => factory(Section::class),
    ];
});
