<?php

namespace App\Http\Controllers;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Jobs\ProcessXapi;
use App\Models\Activity;
use App\Models\EP5\Annotation;
use App\Models\Project;
use App\Models\Section;
use App\Services\Xapi\XapiAnnotationService;
use Illuminate\Http\Request;

class XapiController extends Controller
{
    public function store(Request $request)
    {
        $statement = $request->all();
        ProcessXapi::dispatch($statement);
    }

    public function leftProject(Request $request, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::LEFT),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_LEFT),
            $request->fullUrl(),
            ['en-US' => $project->title],
            $project->id,
            [
                url('/projectId') => $project->id,
                url('/revisionId') => $project->audits()->latest()->first() ? $project->audits()->latest()->first()->id : null,
            ]
        );
    }

    public function openProject(Request $request, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::OPENED),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_OPENED),
            $request->fullUrl(),
            ['en-US' => $project->title],
            $project->id,
            [url('/projectId') => $project->id]
        );
    }

    public function startedEditing(Request $request, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::STARTED_EDIT),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_STARTED_EDIT),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    public function canceledEditing(Request $request, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CANCELED_EDIT),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_CANCELED_EDIT),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    public function revertedVersion(Request $request, Project $project, Section $section, $version)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::REVERTED_VERSION),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_REVERTED_VERSION),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [url('/sectionId') => $section->id,
                url('/revisionId') => $version,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    public function comparedVersions(Request $request, Project $project, Section $section, $version_one, $version_two)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::COMPARED_VERSIONS),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_COMPARED_VERSIONS),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [url('/sectionId') => $section->id,
                url('/revision_one') => $version_one,
                url('/revision_two') => $version_two,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }

    public function viewedHistory(Request $request, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::VIEWED_HISTORY),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_VIEWED_HISTORY),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT));
    }

    public function viewed(Request $request, Project $project, Section $section)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::VIEWED),
            new XapiActivityType(XapiActivityType::SECTION),
            new XapiActivityDescription(XapiActivityDescription::SECTION_VIEWED),
            $request->fullUrl(),
            ['en-US' => $section->title],
            $project->id,
            [
                url('/sectionId') => $section->id,
                url('/revisionId') => $section->audits()->latest()->first() ? $section->audits()->latest()->first()->id : null,
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT));
    }

    public function insertedAnnotation(Request $request, Annotation $annotation)
    {
        XapiAnnotationService::insertedAnnotation($request, $annotation);
    }

    public function clickedActivity(Request $request, Activity $activity)
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
            new XapiVerb(XapiVerb::VIEWED),
            new XapiActivityType(XapiActivityType::ACTIVITY),
            new XapiActivityDescription(XapiActivityDescription::ACTIVITY_VIEWED),
            $request->fullUrl(),
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

    /**
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function clickedMailLink(Request $request, $type)
    {
        $redirectPath = '/';
        try {
            $redirectPath = $request->get('redirectPath');
            $redirectPath = str_replace("(and)", "&", $redirectPath);
            $redirectPath = str_replace("(hash)", "#", $redirectPath);
        } catch (\Exception $ex) {
            \Log::info('Clicked mail url but redirectPath is not set - redirecting to home path');
        }

        $projectId = $request->get('projectId') ? intval($request->get('projectId')) : null;
        $sectionId = $request->get('sectionId') ? intval($request->get('sectionId')) : null;

        Xapi::createStatement(
            new XapiVerb(XapiVerb::VIEWED),
            new XapiActivityType(XapiActivityType::LINK),
            new XapiActivityDescription(XapiActivityDescription::MAIL_LINK_VIEWED),
            $request->fullUrl(),
            ['en-US' => 'A mail link was viewed'],
            $projectId,
            [
                url('/linkType') => $type,
                url('/projectId') => $projectId,
                url('/sectionId') => $sectionId,
            ]
        );

        return redirect($redirectPath);
    }

    public function markAllAsRead(Request $request, Project $project)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::READ_ALL),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_READ_ALL),
            $request->fullUrl(),
            ['en-US' => $project->title],
            $project->id,
            [
                url('/projectId') => $project->id,
                url('/revisionId') => $project->audits()->latest()->first() ? $project->audits()->latest()->first()->id : null,
                url('/triggeredBy') => 'outlineButton'
            ],
            url('projectId/' . $project->id),
            ['en-US' => $project->title],
            new XapiActivityType(XapiActivityType::PROJECT)
        );
    }
}
