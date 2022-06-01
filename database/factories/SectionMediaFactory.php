<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Media;
use App\Models\Section;
use App\Models\SectionMedia;
use Faker\Generator as Faker;

$factory->define(SectionMedia::class, function (Faker $faker) {
    return [
        'ref_id' => $faker->text(10),
        'type' => 'video',
        'mediable_id' => factory(Media::class),
        'section_id' => factory(Section::class),
    ];
});
