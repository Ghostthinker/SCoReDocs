<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Events\ActivityCountEvent;
use App\Events\NewActivityEvent;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Repositories\ActivityRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    private $activityRepository;

    /**
     * ActivityService constructor.
     * @param  ActivityRepositoryInterface  $activityRepository
     */
    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * @param $userId
     * @param $changeMessage
     * @param $sectionId
     * @param $projectId
     * @param  string  $activityType
     */
    public function createAndBroadcastActivity(
        $userId,
        $changeMessage,
        $sectionId,
        $projectId,
        string $activityType = ActivityType::ACTIVITY
    ) {
        $data = [
            'user_id' => $userId,
            'type' => $activityType,
            'message' => $changeMessage,
            'section_id' => $sectionId,
            'project_id' => $projectId
        ];

        $this->createActivityWithBroadcast($data);
    }

    /**
     * add a mentioning message to activities
     *
     * @param $userId
     * @param $message
     * @param $sectionId
     * @param $projectId
     * @param $messageId
     */
    public function createAndBroadcastActivityMentioning(
        $userId,
        $message,
        $sectionId,
        $projectId,
        $messageId
    ) {
        $data = [
            'user_id' => $userId,
            'type' => ActivityType::AT_USER,
            'message' => $message,
            'section_id' => $sectionId,
            'project_id' => $projectId,
            'message_id' => $messageId
        ];

        $activity = $this->activityRepository->create($data);
        $activity = $this->activityRepository->getWithSectionAndProject($activity->id);
        $matches = $this->pregMatchUserPlaceholderToken($activity);
        if (empty($matches)) {
            return;
        }
        $activity = $this->pregReplaceMatchUserPlaceholderToken($activity);
        broadcast(new NewActivityEvent(ActivityResource::make($activity)->additional(['targetUserIds' => $matches[1]])));
    }

    /**
     * @param array $data
     */
    protected function createActivityWithBroadcast(array $data) {
        $activity = $this->activityRepository->create($data);

        $activity = $this->activityRepository->getWithSectionAndProject($activity->id);
        broadcast(new NewActivityEvent(ActivityResource::make($activity)));
        Auth::user()->activity_read()->syncWithoutDetaching($activity->id);

        if ($data['section_id']) {
            broadcast(new ActivityCountEvent($data['project_id'], $data['section_id']))->toOthers();
        }
    }

    /**
     * get all activities with user mentioning; regex all user id tokens [[user:number]] by name
     *
     * @param array $projectIds
     * @return mixed
     */
    public function getActivitiesByProjectsFromUser(array $projectIds = []) {
        if (empty($projectIds)) {
            $user = Auth::user();
            $projectIds = $user->project_watch()->pluck('project_id')->toArray();
        }
        $activities = $this->activityRepository->fetchByProjectIdsWithMentionings($projectIds);
        foreach ($activities as &$activity) {
            $activity = $this->pregReplaceMatchUserPlaceholderToken($activity);
        }
        return $activities;
    }

    /**
     * @param $activity
     *
     * @return array
     */
    protected function pregMatchUserPlaceholderToken($activity): array
    {
        $other = "/\[\[user:(\d+)]]/";
        $matches = [];
        preg_match_all($other, $activity->message, $matches);
        return $matches;
    }

    /**
     * @param       $activity
     * @param array $matches
     *
     * @return Activity
     */
    protected function pregReplaceUserPlaceholderToken($activity, array $matches): Activity
    {
        if (empty($matches)) {
            return $activity;
        }
        foreach ($matches[1] as $uid) {
            $otherRegx = "/\[\[user:$uid]]/";
            $otherUser = User::find($uid);
            if ($otherUser !== null) {
                $activity->message = preg_replace($otherRegx, '@'.$otherUser->getNameAttribute(), $activity->message);
            }
        }
        return $activity;
    }

    /**
     * @param $activity
     *
     * @return Activity
     */
    protected function pregReplaceMatchUserPlaceholderToken($activity): Activity
    {
        $matches = $this->pregMatchUserPlaceholderToken($activity);
        return $this->pregReplaceUserPlaceholderToken($activity, $matches);
    }
}
