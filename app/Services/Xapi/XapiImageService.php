<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Models\Section;
use App\Models\SectionMedia;
use App\Repositories\ProjectRepository;

class XapiImageService
{
    /**
     * @param string $requestUrl
     * @param SectionMedia $media
     * @param Section $section
     */
    public static function storeImage(string $requestUrl, SectionMedia $media, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::IMAGE),
            new XapiActivityDescription(XapiActivityDescription::IMAGE_CREATED),
            $requestUrl . '/images/' . $media->id . '/created',
            ['en-US' => 'image'],
            $projectId,
            [url('/refId') => $media->ref_id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION)
        );
    }

    /**
     * @param string $requestUrl
     * @param SectionMedia $media
     * @param Section $section
     */
    public static function storeVideo(string $requestUrl, SectionMedia $media, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription(XapiActivityDescription::VIDEO_CREATED),
            $requestUrl . '/videos/' . $media->id . '/created',
            ['en-US' => 'video'],
            $projectId,
            [url('/refId') => $media->ref_id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION)
        );
    }

    /**
     * @param string $requestUrl
     * @param SectionMedia $media
     * @param Section $section
     */
    public static function destroyVideo(string $requestUrl, SectionMedia $media, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::VIDEO),
            new XapiActivityDescription(XapiActivityDescription::VIDEO_DELETED),
            $requestUrl . '/videos/' . $media->id . '/deleted',
            ['en-US' => 'video'],
            $projectId,
            [url('/refId') => $media->ref_id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION)
        );
    }

    /**
     * @param String $requestUrl
     * @param SectionMedia $media
     * @param Section $section
     */
    public static function destroyImage(string $requestUrl, SectionMedia $media, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::IMAGE),
            new XapiActivityDescription(XapiActivityDescription::IMAGE_DELETED),
            $requestUrl . '/images/' . $media->id . '/deleted',
            ['en-US' => 'image'],
            $projectId,
            [url('/refId') => $media->ref_id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION)
        );
    }
}
