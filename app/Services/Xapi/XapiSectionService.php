<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Http\Resources\SectionResource;
use App\Models\Project;
use App\Models\Section;

class XapiSectionService
{
    /**
     * @param  string  $objectId
     * @param  SectionResource  $section
     * @param  Project  $project
     */
    public static function storeSection(string $objectId, SectionResource $section, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_CREATED),
            $objectId,
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('projectId/'.$project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    /**
     * @param  string  $objectId
     * @param  Project  $project
     */
    public static function getSections(string $objectId, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::OPENED),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_OPENED),
            $objectId,
            ['en-US' => $project->title],
            $project->id,
            [
                url('/projectId') => $project->id,
                url('/revisionId') => $project->audits()->latest()->first() ? $project->audits()->latest()->first()->id : null,
            ]
        );
    }

    /**
     * @param $objectId
     * @param $data
     * @param $sectionId
     * @param $project
     * @param $audit
     * @param $changedProperties
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function updateSection($objectId, $data, $sectionId, $project, $audit, $changedProperties)
    {
        $changedProperty = implode(',', $changedProperties);
        $data['fullUrl'] = $objectId;
        $data['title'] = ['en-US' => $data['title']];
        $data['parentId'] = url('projectId/' . $project->id);

        self::createStatement(
            XapiVerb::UPDATED, XapiActivityDescription::SECTION_UPDATED,
            $project, $data, $project->id,
            [
                url('/sectionId') => $sectionId,
                url('/revisionId') => $audit ? $audit->id : null,
                url('/changedProperty') => $changedProperty,
            ]
        );
    }

    /**
     * @param  string  $objectId
     * @param  Project  $project
     */
    public static function revertSection(string $objectId, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::REVERTED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_REVERTED),
            $objectId,
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : 0,
            ],
            url('projectId/'.$project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    /**
     * @param  string  $objectId
     * @param  Project  $project
     */
    public static function destroySection(string $objectId, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_DELETED),
            $objectId,
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : 0,
            ],
            url('projectId/'.$project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    /**
     * @param $objectId
     * @param  Section  $section
     * @param  Project  $project
     */
    public static function openedPlaylist($objectId, Section $section, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::OPENED),
            new XapiActivityType(XapiActivityType::PLAYLIST),
            new XapiActivityDescription(XapiActivityDescription::PLAYLIST_OPENED),
            $objectId,
            ['en-US' => 'Playlist von Abschnitt: '.$section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first()->id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }

    /**
     * @param $objectId
     * @param  Section  $section
     * @param  Project  $project
     */
    public static function downloadedPlaylist($objectId, Section $section, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DOWNLOADED),
            new XapiActivityType(XapiActivityType::PLAYLIST),
            new XapiActivityDescription(XapiActivityDescription::PLAYLIST_DOWNLOADED),
            $objectId,
            ['en-US' => 'Playlist von Abschnitt: '.$section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first()->id,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }

    /**
     * @param      $verb
     * @param      $description
     * @param      $parent
     * @param      $data
     * @param      $projectId
     * @param null $extensions
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    private static function createStatement($verb, $description, $parent, $data, $projectId, $extensions = null): void
    {
        $parentId = $data['parentId'];
        $contextType = strrpos($parentId, 'section') !== false ? XapiActivityType::SECTION : XapiActivityType::PROJECT;
        $parentTitle = $parent !== null ? ['en-US' => $parent->title] : ['en-US' => ''];
        Xapi::createStatement(
            new XapiVerb($verb),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription($description),
            $data['fullUrl'],
            $data['title'],
            $projectId,
            $extensions,
            $parentId,
            $parentTitle,
            new XapiActivityType($contextType)
        );
    }

    /**
     * @param string  $objectId
     * @param Section $section
     * @param Project $project
     * @param         $audit
     *
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function clickMinorUpdate(string $objectId, Section $section, Project $project, $audit)
    {
        $data = [];
        $data['fullUrl'] = $objectId;
        $data['title'] = ['en-US' => 'checkbox for minor update of : '. $section->title];
        $data['parentId'] = url('projectId/' . $project->id);

        self::createStatement(
            XapiVerb::CLICKED, XapiActivityDescription::SECTION_CLICKED_MINOR,
            $project, $data, $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $audit ? $audit->id : null,
            ]
        );
    }

    /**
     * @param $objectId
     * @param  Section  $section
     * @param  $projectId
     */
    public static function expandedSection($objectId, Section $section, $projectId)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::EXPANDED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_EXPANDED),
            $objectId,
            ['en-US' => $section->title],
            $projectId,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }

    /**
     * @param $objectId
     * @param  Section  $section
     * @param  $projectId
     */
    public static function collapsedSection($objectId, Section $section, $projectId)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::COLLAPSED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_COLLAPSED),
            $objectId,
            ['en-US' => $section->title],
            $projectId,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('sectionId/' . $section->id),
            ['en-US' => $section->title],
            new XapiActivityType(XapiActivityType::SECTION));
    }
}
