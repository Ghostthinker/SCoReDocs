<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Repositories\ActivityRepositoryInterface;
use App\Services\ActivityService;
use App\Services\Xapi\XapiActivityService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * @param  Request  $request
     * @param ActivityService $activityService,
     * @return AnonymousResourceCollection
     */
    public function getActivitiesForUser(Request $request, ActivityService $activityService): AnonymousResourceCollection
    {
        return ActivityResource::collection($activityService->getActivitiesByProjectsFromUser());
    }

    /**
     * @param  Request  $request
     * @param  number  $projectId
     * @param  ActivityRepositoryInterface  $activityRepository
     * @return AnonymousResourceCollection
     */
    public function getActivitiesForProjectId(Request $request, $projectId, ActivityService $activityService): AnonymousResourceCollection
    {
        return ActivityResource::collection($activityService->getActivitiesByProjectsFromUser([$projectId]));
    }

    public function getActivitiesForProjectIdBySectionId(Request $request, $projectId, $sectionId, ActivityRepositoryInterface $activityRepository)
    {
        return ActivityResource::collection($activityRepository->paginationByProjectIdBySectionId($projectId, $sectionId));
    }

    /**
     * @param  Request  $request
     * @param  number  $projectId
     * @param  ActivityRepositoryInterface  $activityRepository
     * @return mixed
     */
    public function getUnreadActivitiesCount(Request $request, $projectId, ActivityRepositoryInterface $activityRepository)
    {
        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($projectId, Auth::id());
        return $project->sections->map(function ($item) {
            return [
              "section_id" => $item->id,
              "activities_count" => $item->activities_count
            ];
        });
    }

    public function markAllActivitiesAsRead(Request $request, $projectId, ActivityRepositoryInterface $activityRepository) {
        $activityRepository->markAllByProjectAsRead($projectId, Auth::id());
    }

    /**
     * @param  Request  $request
     * @param  Activity  $activity
     */
    public function markAsRead(Request $request, Activity $activity)
    {
        Auth::user()->activity_read()->syncWithoutDetaching($activity->id);
        XapiActivityService::read($request->fullUrl(), $activity);
    }
}
