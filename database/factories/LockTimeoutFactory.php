<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\LockTimeout;
use App\Models\Section;
use Faker\Generator as Faker;

$factory->define(LockTimeout::class, function (Faker $faker) {
    return [
        'section_id' => factory(Section::class),
    ];
});
