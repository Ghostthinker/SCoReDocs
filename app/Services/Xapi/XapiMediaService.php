<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;

class XapiMediaService
{
    public static function create($parent, $media, $data, $projectId = null)
    {
        self::createStatement(
            XapiVerb::UPLOADED,
            XapiActivityDescription::MEDIA_UPLOADED,
            $parent, $media, $data, $projectId
        );
    }

    public static function open($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::OPENED,
            XapiActivityDescription::MEDIA_OPENED,
            $parent, $media, $data
        );
    }

    public static function play($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::PLAYED,
            XapiActivityDescription::MEDIA_PLAYED,
            $parent, $media, $data
        );
    }

    public static function pause($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::PAUSED,
            XapiActivityDescription::MEDIA_PAUSED,
            $parent, $media, $data
        );
    }

    public static function end($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::ENDED,
            XapiActivityDescription::MEDIA_ENDED,
            $parent, $media, $data
        );
    }

    public static function leave($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::LEFT,
            XapiActivityDescription::MEDIA_LEFT,
            $parent, $media, $data
        );
    }

    public static function seeked($parent, $media, $data)
    {
        self::createStatement(
            XapiVerb::SEEKED,
            XapiActivityDescription::MEDIA_SEEKED,
            $parent, $media, $data
        );
    }

    /**
     * @param $verb
     * @param $description
     * @param $parent
     * @param $media
     * @param $data
     * @param  null  $projectId
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    private static function createStatement($verb, $description, $parent, $media, $data, $projectId = null): void
    {
        $parentId = $parent !== null ? url('/sectionId/' . $parent->id) : null;
        $parentTitle = $parent !== null ? ['en-US' => $parent->title] : ['en-US' => ''];
        $mediaCaption = $media->caption ? $media->caption : 'Kein Titel vorhanden.';

        Xapi::createStatement(
            new XapiVerb($verb),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription($description),
            $data['id'],
            ['en-US' => 'Media: ' . $data['title']],
            $projectId,
            [
                url('/caption') => $mediaCaption,
                url('/mediaId') => $media->id,
                url('/streamingId') => $media->streamingId,
                url('/currentTime') => $data['currentTime'],
                url('/mediaType') => array_key_exists('sequenceId', $data) && $data['sequenceId'] ? 'Sequence' : 'Video',
                url('/mediaSequenceId') => array_key_exists('sequenceId', $data) ? $data['sequenceId'] : null,
                url('/isPlaylist') => array_key_exists('isPlaylist', $data) ? $data['isPlaylist'] : null,
            ],
            $parentId,
            $parentTitle,
            new XapiActivityType(XapiActivityType::SECTION)
        );
    }
}
