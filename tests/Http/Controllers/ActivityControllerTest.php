<?php

namespace Tests\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Section;
use App\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
        $this->user = factory(User::class)->create();
    }

    public function testGetActivitiesForUserId()
    {
        $response = $this->actingAs($this->user)->get("rest/activities");
        $response->assertStatus(200);
    }

    public function testGetActivitiesForProjectId()
    {
        $project = factory(Project::class)->create();
        factory(Activity::class)->create(['project_id' => $project->id]);
        $response = $this->actingAs($this->user)->get("rest/activities/project/" . $project->id);
        $response->assertStatus(200);
        $activity = $response->getOriginalContent()->first();
        $this->assertEquals($project->id, $activity->project_id);
    }

    public function testGetActivitiesForProjectIdBySectionId() {
        $project = factory(Project::class)->create();
        factory(Activity::class)->create(['project_id' => $project->id]);
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);
        factory(Activity::class)->create([
            'project_id' => $project->id,
            'section_id' => $section->id
        ]);

        $response = $this->actingAs($this->user)->get("rest/activities/project/" . $project->id . '/section/' . $section->id);
        $response->assertStatus(200);
        $activity = $response->getOriginalContent()->first();
        $this->assertEquals($section->id, $activity->section_id);

        factory(Activity::class)->create([
            'project_id' => $project->id,
            'section_id' => null
        ]);
        $response = $this->actingAs($this->user)->get("rest/activities/project/" . $project->id . '/section/' . $section->id);
        $response->assertStatus(200);
        $activity = $response->getOriginalContent();
        $activity->count();
        $this->assertEquals(1, $activity->count());

        factory(Activity::class)->create([
            'project_id' => $project->id,
            'section_id' => $section->id
        ]);
        $response = $this->actingAs($this->user)->get("rest/activities/project/" . $project->id . '/section/' . $section->id);
        $response->assertStatus(200);
        $activity = $response->getOriginalContent();
        $activity->count();
        $this->assertEquals(2, $activity->count());
    }

    public function testMarkAsRead()
    {
        $project = factory(Project::class)->create();
        $activity = factory(Activity::class)->create(['project_id' => $project->id]);
        $response = $this->actingAs($this->user)->get("rest/activity/" .$activity->id. "/markAsRead");
        $response->assertStatus(200);
        $this->assertTrue($this->user->activity_read()->exists());
    }

    public function testGetUnreadActivitiesCount() {
        $project = factory(Project::class)->create();
        factory(Activity::class)->create(['project_id' => $project->id]);
        $response = $this->actingAs($this->user)->get("rest/getUnreadActivitiesCount/project/" . $project->id);
        $response->assertStatus(200);
    }
}
