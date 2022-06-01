<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;

class XapiVideoSequenceService
{
    public static function create($parent, $sequence, $objectId)
    {
        self::createStatement(
            XapiVerb::CREATED,
            XapiActivityDescription::SEQUENCE_CREATED,
            $parent, $sequence, $objectId
        );
    }

    public static function update($parent, $sequence, $objectId)
    {
        self::createStatement(
            XapiVerb::UPDATED,
            XapiActivityDescription::SEQUENCE_UPDATED,
            $parent, $sequence, $objectId
        );
    }

    /**
     * @param $verb
     * @param $description
     * @param $parent
     * @param $sequence
     * @param $objectId
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    private static function createStatement($verb, $description, $parent, $sequence, $objectId): void
    {
        $parentId = $parent !== null ? url('/mediaId/' . $parent->id) : null;
        $parentCaption =  $parent->caption ?  $parent->caption : '';
        $parentTitle = $parent !== null ? ['en-US' => $parentCaption ] : ['en-US' => ''];

        Xapi::createStatement(
            new XapiVerb($verb),
            new XapiActivityType(XapiActivityType::SEQUENCE),
            new XapiActivityDescription($description),
            $objectId,
            ['en-US' => 'Sequence: ' . $sequence->title],
            null,
            [
                url('/sequenceTitle') => $sequence->title,
                url('/mediaId') => $sequence->video_nid,
                url('/sequenceDescription') => $sequence->description,
                url('/sequenceDuration') => $sequence->duration,
                url('/sequenceTimestamp') => $sequence->timestamp,
                url('/sequenceCameraPath') => $sequence->camera_path
            ],
            $parentId,
            $parentTitle,
            new XapiActivityType(XapiActivityType::SEQUENCE)
        );
    }
}
