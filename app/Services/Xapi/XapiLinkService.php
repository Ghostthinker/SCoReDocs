<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Models\Section;
use App\Repositories\ProjectRepository;

class XapiLinkService
{
    /**
     * @param  string  $requestUrl
     * @param  array  $link
     * @param  Section  $section
     * @param  ProjectRepositoryInterface  $projectRepository
     */
    public static function createLink(string $requestUrl, array $link, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::LINK),
            new XapiActivityDescription(XapiActivityDescription::LINK_CREATED),
            $requestUrl.'/created',
            ['en-US' => 'link'],
            $projectId,
            [url('/linkId') => $link['id'],
                url('/linkType') => $link['type'],
                url('/origin') => $link['origin'],
                url('/target') => $link['target'],
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }

    /**
     * @param string $requestUrl
     * @param array $link
     * @param Section $section
     */
    public static function destroyLink(string $requestUrl, array $link, Section $section)
    {
        $projectRepository = new ProjectRepository();

        $project = $projectRepository->get($section->project_id);
        $projectId = $project ? $project->id : null;
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::LINK),
            new XapiActivityDescription(XapiActivityDescription::LINK_DELETED),
            $requestUrl.'/deleted',
            ['en-US' => 'link'],
            $projectId,
            [url('/linkId') => $link['id'],
                url('/linkType') => $link['type'],
                url('/origin') => $link['origin'],
                url('/target') => $link['target'],
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }
}
