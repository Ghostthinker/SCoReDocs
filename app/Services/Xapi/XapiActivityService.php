<?php


namespace App\Services\Xapi;


use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Models\Activity;

class XapiActivityService
{
    public static function read(string $objectId, Activity $activity)
    {
        if ($activity->section_id !== null) {
            $parent = $activity->section;
            $parentId = url('/sectionId/' . $activity->section_id);
            $parentType = XapiActivityType::SECTION;
        } else {
            $parent = $activity->project;
            $parentId = url('/projectId/' . $activity->project_id);
            $parentType = XapiActivityType::PROJECT;
        }
        $parentTitle = $parent !== null ? ['en-US' => $parent->title] : ['en-US' => ''];
        Xapi::createStatement(
            new XapiVerb(XapiVerb::READ),
            new XapiActivityType(XapiActivityType::ACTIVITY),
            new XapiActivityDescription(XapiActivityDescription::ACTIVITY_READ),
            $objectId,
            ['en-US' => $activity->message],
            $activity->project_id,
            [
                url('/sectionId') => $activity->section_id,
                url('/projectId') => $activity->project_id,
                url('/activityId') => $activity->id,
                url('/activityType') => $activity->type,
                url('/activityMessage') => $activity->message,
            ],
            $parentId,
            $parentTitle,
            new XapiActivityType($parentType)
        );
    }
}
