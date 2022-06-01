<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DataExport;
use Faker\Generator as Faker;

$factory->define(DataExport::class, function (Faker $faker) {
    return [
        'filename' => $faker->text(191),
        'path' => $faker->text(191),
        'statement_count' => $faker->numberBetween(),
        'downloaded_count' => $faker->numberBetween(),
        'filesize' => $faker->numberBetween(),
        'created_at' => now(),
        'updated_at' => now()
    ];
});
