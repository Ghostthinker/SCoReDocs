<?php

namespace App\Repositories;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityRepository implements ActivityRepositoryInterface
{
    /**
     * @param  array  $data
     * @return mixed|null
     */
    public function create(array $data)
    {
        $activity = Activity::make($data);
        if($activity->save()) {
            return $activity;
        }
        return null;
    }

    public function paginationByProjectId($projectId) {
        return Activity::where('project_id', $projectId)->with(['section' => function($q) { $q->withTrashed(); }, 'project', 'user_read' => function($q) { $q->where('user_id', Auth::user()->id); }])->latest()->paginate(25);
    }

    public function paginationByProjectIdBySectionId($projectId, $sectionId)
    {
        return Activity::where('project_id', $projectId)->where('section_id', $sectionId)->with(['section' => function($q) { $q->withTrashed(); }, 'project', 'user_read' => function($q) { $q->where('user_id', Auth::user()->id); }])->latest()->paginate(25);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function fetchByProjectIds($projectIds) {
        return Activity::whereIn('project_id', $projectIds)->with(['section' => function($q) { $q->withTrashed(); }, 'project', 'user_read' => function($q) { $q->where('user_id', Auth::user()->id); }])->latest()->paginate(25);
    }

    public function fetchByProjectIdsWithMentionings($projectIds) {
        $query = Activity::whereIn('project_id', $projectIds)
            ->whereNull('message_id')
            ->orWhereExists(function ($query) {
                $query->select(DB::raw(1))->from('message_mentionings');
                $query->where('message_mentionings.user_id', '=', Auth::user()->id);
                $query->whereRaw('message_mentionings.message_id = activities.message_id');
            })
            ->with([
                'section' => function($q) { $q->withTrashed(); },
                'project',
                'user_read' => function($q) { $q->where('user_id', Auth::user()->id); }
            ]
        );
        return $query->latest()->paginate(25);
    }

    public function getWithSectionAndProject($id)
    {
        return Activity::where('id', $id)->with(['section' => function($q) { $q->withTrashed(); }, 'project', 'user_read' => function($q) { $q->where('user_id', Auth::user()->id); }])->first();
    }

    public function getCountOfUnreadActivitiesByProjectAndUser($projectId, $userId)
    {
        return Project::where('id', $projectId)->with([
            'sections' => function ($q) use ($userId) {
                $q->withCount([
                    'activities' => function ($q) use ($userId) {
                        $q->whereDoesntHave('user_read', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        });
                    }
                ]);
            }
        ])->first();
    }

    public function markAllByProjectAsRead($projectId, $userId)
    {
        $activities = Activity::where('project_id', $projectId)->get();
        foreach ($activities as $activity) {
            $activity->user_read()->syncWithoutDetaching($userId);
        }
    }

    public function get($id)
    {
        return Activity::find($id);
    }
}
