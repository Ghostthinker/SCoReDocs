<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use App\Models\Media;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Media::class, 'created', function ($faker) {



    return [
        'status' => 'created',
    ];
});
