<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message.{projectId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('message.{projectId}-section.{sectionId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('messagecount.{projectId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('messagecount.{projectId}-section.{sectionId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('activitycount.{projectId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('messageMention.{projectId}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('activity', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('section', function () {
    return true;
});

Broadcast::channel('project', function () {
    return true;
});

Broadcast::channel('news', function () {
    return true;
});

Broadcast::channel('user.{id}', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

