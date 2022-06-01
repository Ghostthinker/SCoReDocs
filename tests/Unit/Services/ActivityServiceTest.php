<?php

namespace Tests\Unit\Services;

use App\Events\NewActivityEvent;
use App\Models\Message;
use App\Models\MessageMentionings;
use App\Models\Project;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
{
    use RefreshDatabase;

    private $activityService;
    private $activityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activityRepository = $this->app->make(ActivityRepository::class);
        $this->activityService = new ActivityService(
            $this->activityRepository
        );

        Event::fake();
        Queue::fake();
    }

    public function testCreateAndBroadcastActivity()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $this->activityService->createAndBroadcastActivity($user->id, 'test', null, $project->id);

        Event::assertDispatched(function (NewActivityEvent $event) use ($project) {
            $activityResource = $event->getActivity();
            return $project->id === (int) $activityResource->project_id;
        });
        $activityNew = $this->activityRepository->fetchByProjectIds([$project->id]);
        $this->assertEquals(1, count($activityNew));
    }

    public function testGetActivitiesByProjectsFromUser()
    {
        $project = factory(Project::class)->create();
        $actor = factory(User::class)->create();
        $userOne = factory(User::class)->create();
        $userTwo = factory(User::class)->create();

        $message = factory(Message::class)->create([
            'user_id' => $actor->id,
            'project' => $project->id,
            'section_id' => null,
            'type' => 'text',
            'data' => ['text' => "test - [[user:$userTwo->id]] nice [[user:$userOne->id]] nice"],
            'parent_id' => null,
            'at_all_mentioning' => 0
        ]);

        $mentioning = factory(MessageMentionings::class)->create([
            'user_id' => $userOne->id,
            'message_id' => $message->id,
            'project_id' => $project->id
        ]);

        $this->be($actor);

        // simple activity
        $this->activityService->createAndBroadcastActivity($actor->id, 'test', null, $project->id);

        // mentioning message => activity
        $this->activityService->createAndBroadcastActivityMentioning(
            $actor->id,
            $message->data['text'],
            null,
            $project->id,
            $message->id
        );
        $activities = $this->activityRepository->fetchByProjectIdsWithMentionings([$project->id]);
        $this->assertEquals(1, count($activities));

        $this->be($userOne);
        $activities = $this->activityRepository->fetchByProjectIdsWithMentionings([$project->id]);
        $this->assertEquals(2, count($activities));

        // check service with regex pattern (name)
        $activities = $this->activityService->getActivitiesByProjectsFromUser([$project->id]);
        $this->assertEquals(2, count($activities));
        $this->assertStringContainsString('@'.$userOne->getNameAttribute(), $activities[1]->message);
        $this->assertStringContainsString('@'.$userTwo->getNameAttribute(), $activities[1]->message);

        // user two, no mentioning = one activity
        $this->be($userTwo);
        $activities = $this->activityRepository->fetchByProjectIdsWithMentionings([$project->id]);
        $this->assertEquals(1, count($activities));

        // check service, normal activity => without regex name (assertStringNotContainsString)
        $activities = $this->activityService->getActivitiesByProjectsFromUser([$project->id]);
        $this->assertEquals(1, count($activities));
        $this->assertStringNotContainsString('@'.$userOne->getNameAttribute(), $activities[0]->message);
        $this->assertStringNotContainsString('@'.$userTwo->getNameAttribute(), $activities[0]->message);

        // add mentioning for user two => now two activities (activity normal + activity with mentioning)
        $mentioning = factory(MessageMentionings::class)->create([
            'user_id' => $userTwo->id,
            'message_id' => $message->id,
            'project_id' => $project->id
        ]);
        $activities = $this->activityRepository->fetchByProjectIdsWithMentionings([$project->id]);
        $this->assertEquals(2, count($activities));

        // check service with regex pattern (name)
        $activities = $this->activityService->getActivitiesByProjectsFromUser([$project->id]);
        $this->assertEquals(2, count($activities));
        $this->assertStringContainsString('@'.$userOne->getNameAttribute(), $activities[1]->message);
        $this->assertStringContainsString('@'.$userTwo->getNameAttribute(), $activities[1]->message);
    }
}
