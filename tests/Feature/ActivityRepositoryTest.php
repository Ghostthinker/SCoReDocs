<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Section;
use App\Repositories\ActivityRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testGetCountOfUnreadActivitiesByProjectAndUser()
    {
        $activityRepository = new ActivityRepository();

        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create(['project_id' => $project->id]);
        $activity = factory(Activity::class)->create([
            'project_id' => $project->id,
            'section_id' => $section->id
        ]);

        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($project->id, $this->user->id);
        $unreadActivities = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "activities_count" => $item->activities_count
            ];
        });
        $this->assertEquals($unreadActivities[0]['section_id'], $section->id);
        $this->assertEquals($unreadActivities[0]['activities_count'], 1);

        $user2 = factory(User::class)->create();
        $activity->user_read()->attach($user2->id);

        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($project->id, $user2->id);
        $unreadActivities = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "activities_count" => $item->activities_count
            ];
        });
        $this->assertEquals($unreadActivities[0]['section_id'], $section->id);
        $this->assertEquals($unreadActivities[0]['activities_count'], 0);
    }

    public function testMarkAllByProjectAsRead() {
        $activityRepository = new ActivityRepository();

        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create(['project_id' => $project->id]);
        factory(Activity::class)->create([
            'project_id' => $project->id,
            'section_id' => $section->id
        ]);

        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($project->id, $this->user->id);
        $unreadActivities = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "activities_count" => $item->activities_count
            ];
        });
        $this->assertEquals($unreadActivities[0]['activities_count'], 1);

        $activityRepository->markAllByProjectAsRead($project->id, $this->user->id);
        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($project->id, $this->user->id);
        $unreadActivities = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "activities_count" => $item->activities_count
            ];
        });
        $this->assertEquals($unreadActivities[0]['activities_count'], 0);

        $user2 = factory(User::class)->create();
        $project = $activityRepository->getCountOfUnreadActivitiesByProjectAndUser($project->id, $user2->id);
        $unreadActivities = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "activities_count" => $item->activities_count
            ];
        });
        $this->assertEquals($unreadActivities[0]['activities_count'], 1);
    }
}
