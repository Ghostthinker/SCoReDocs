<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;

class XapiPlaybackCommandService
{
    public static function updatePlaybackCommand($playbackCommand, $data, $fullUrl)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::UPDATED),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription(XapiActivityDescription::PLAYBACK_COMMAND_UPDATED),
            $fullUrl,
            ['en-US' => 'PlaybackCommand: ' . $data['title']],
            null,
            [
                url('/videoId') => $data['video_nid'],
                url('/playbackId') => $playbackCommand->id,
                url('/type') => $data['type'],
                url('/additionalFields') => $data['additional_fields'],
                url('/revisionId') => $playbackCommand->audits()->latest()->first()->id,
            ],
            url('/videoId/' . $data['video_nid']),
            ['en-US' => ''],
            new XapiActivityType(XapiActivityType::VIDEO)
        );
    }

    public static function deletePlaybackCommand($playbackCommand, $fullUrl)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription(XapiActivityDescription::PLAYBACK_COMMAND_DELETED),
            $fullUrl,
            ['en-US' => 'PlaybackCommand: ' . $playbackCommand->title],
            null,
            [
                url('/videoId') => $playbackCommand->video_nid,
                url('/playbackId') => $playbackCommand->id,
                url('/revisionId') => $playbackCommand->audits()->latest()->first()->id,
            ],
            url('/videoId/' . $playbackCommand->video_nid),
            ['en-US' => ''],
            new XapiActivityType(XapiActivityType::VIDEO)
        );
    }

    public static function createPlaybackCommand($playbackCommand, $data, $fullUrl)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription(XapiActivityDescription::PLAYBACK_COMMAND_CREATED),
            $fullUrl,
            ['en-US' => 'PlaybackCommand: ' . $data['title']],
            null,
            [
                url('/videoId') => $data['video_nid'],
                url('/playbackId') => $playbackCommand->id,
                url('/type') => $data['type'],
                url('/additionalFields') => $data['additional_fields'],
                url('/revisionId') => $playbackCommand->audits()->latest()->first()->id,
            ],
            url('/videoId/' . $data['video_nid']),
            ['en-US' => ''],
            new XapiActivityType(XapiActivityType::VIDEO)
        );
    }
}
