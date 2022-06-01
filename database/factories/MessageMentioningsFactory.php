<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Message;
use App\Models\MessageMentionings;
use App\Models\Project;
use App\User;
use Faker\Generator as Faker;

$factory->define(MessageMentionings::class, function (Faker $faker) {
    return [
        'project_id' => factory(Project::class),
        'message_id' => factory(Message::class),
        'user_id' => factory(User::class),
    ];
});
