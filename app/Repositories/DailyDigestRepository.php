<?php


namespace App\Repositories;

use App\Models\Media;
use App\Models\MessageMentionings;
use App\Models\Project;
use App\Models\Section;

class DailyDigestRepository implements DailyDigestRepositoryInterface
{

    public function getNewCreatedSectionAmount($project, $startDate, $endDate)
    {
        return $project->sections->whereBetween('created_at', [$startDate, $endDate])->count();
    }

    public function getChangedSectionsAmount($project, $startDate, $endDate)
    {
        $count = 0;
        $sections = Section::where('project_id', $project->id)->get();
        foreach ($sections as $section) {
            $count += $this->getChangedSectionAmount($section, $startDate, $endDate);
        }
        return $count;
    }

    public function getNewCreatedVideoAmount($project, $startDate, $endDate)
    {
        $sections = $project->sections()->get();
        $mediaIds = [];
        foreach ($sections as $section) {
            $tempMediaIds = $section->section_media()->where('type', 'video')->pluck('mediable_id');
            $mediaIds = array_merge($mediaIds, $tempMediaIds->toArray());
        }

        return Media::whereIn('id', $mediaIds)->whereBetween('created_at', [$startDate, $endDate])->count();

    }

    public function getNewCreatedAnnotationAmount($project, $startDate, $endDate)
    {
        $sections = $project->sections()->get();
        $mediaIds = [];
        foreach ($sections as $section) {
            $tempMediaIds = $section->section_media()->where('type', 'video')->pluck('mediable_id');
            $mediaIds = array_merge($mediaIds, $tempMediaIds->toArray());
        }

        $medias = Media::whereIn('id', $mediaIds)->get();

        $annotationsCount = 0;
        foreach ($medias as $media) {
            $annotationsCount += $media->annotations()->whereBetween('created_at', [$startDate, $endDate])->count();
        }
        return $annotationsCount;
    }

    /**
     * Get the number of changes of the sections a user was involved
     *
     * @param  Project  $project
     * @param  string  $user_id
     * @param  string  $startDate
     * @param  string  $endDate
     * @return array
     */
    public function getInvolvedSectionChangesAmount($project, $user_id, $startDate, $endDate): array
    {
        $sections = $project->sections()->get();
        $involvedSections = [];
        foreach ($sections as $section) {
            $userWasInvolvedInSection = $section->audits()
                ->where('user_id', $user_id)->where('is_minor_update', 0)->exists();
            if ($userWasInvolvedInSection) {
                array_push($involvedSections, $section);
            }
        }
        $involvedSections = array_unique($involvedSections);

        $result = [];
        foreach ($involvedSections as $section) {
            $count = $this->getChangedSectionAmount($section, $startDate, $endDate);
            if ($count > 0) {
                array_push($result, ['count' => $count, 'section' => $section, 'project' => $project]);
            }
        }

        return $result;
    }

    /**
     * Get the changes amount of single section
     *
     * @param  Section  $section
     * @param  string  $startDate
     * @param  string  $endDate
     * @return int
     */
    public function getChangedSectionAmount($section, $startDate, $endDate): int
    {
        return $section->audits()->where('event', 'updated')->where('is_minor_update', 0)->whereBetween('created_at', [$startDate, $endDate])->count();
    }


    public function getAmountOfProjectsUserWasMentionedFromTo($userId, $from, $to)
    {
        return MessageMentionings::where('user_id', $userId)->whereBetween('created_at', [$from, $to])->groupBy('project_id')->get()->count();
    }

    public function getAtAllMentioningOfProjectByUserId($project, $userId)
    {
        return MessageMentionings::where('user_id', $userId)->where('project_id', $project->id)->whereNull('read_at')->whereHas('message', function($q) { $q->where('at_all_mentioning', 1); } )->with('message')->get();
    }

}
