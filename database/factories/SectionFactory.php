<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\SectionStatus;
use App\Models\Project;
use App\Models\Section;
use App\User;
use Faker\Generator as Faker;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'title' => $faker->text(10),
        'content' => $faker->text(),
        'heading' => 3,
        'index' => $faker->randomNumber(),
        'locked' => false,
        'locked_at' => null,
        'locking_user' => null,
        'project_id' => factory(Project::class),
        'status' => SectionStatus::IN_PROGRESS,
        'author_id' => factory(User::class)
    ];
});
