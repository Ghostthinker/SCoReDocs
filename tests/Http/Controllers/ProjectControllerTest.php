<?php

namespace Tests\Http\Controllers;

use App\Events\NewProjectEvent;
use App\Events\UpdateProjectEvent;
use App\Jobs\ProcessXapi;
use App\Models\Project;
use App\Rules\Roles;
use App\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
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

    public function testStoreProject()
    {
        $projectResponse = $this->actingAs($this->user)->post("rest/project",
            [
                'title' => 'Title',
                'description' => 'Beschreibung'
            ]
        );
        $projectResponse->assertStatus(403);
        $this->user->givePermissionTo('edit projects');
        $projectResponse = $this->actingAs($this->user)->post("rest/project",
            [
                'title' => 'Title',
                'description' => 'Beschreibung'
            ]
        );
        $projectResponse->assertStatus(201);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testGetProjects()
    {
        $projectResponse = $this->actingAs($this->user)->get("rest/projects");
        $projectResponse->assertStatus(200);
    }

    public function testGetProject()
    {
        $project = factory(Project::class)->create();
        $projectResponse = $this->actingAs($this->user)->get("rest/project/" . $project->id);
        $projectResponse->assertStatus(200);
        $this->assertEquals($project->id, $projectResponse->getOriginalContent()->id);
    }

    public function testUpdateProject()
    {
        $project = factory(Project::class)->create();
        $projectResponse = $this->actingAs($this->user)->put("rest/project/" . $project->id, ['title' => 'Test']);
        $projectResponse->assertStatus(403);

        $this->user->givePermissionTo('edit projects');
        $projectResponse = $this->actingAs($this->user)->put("rest/project/" . $project->id, ['title' => 'Test']);
        $projectResponse->assertStatus(200);

        Event::assertDispatched(UpdateProjectEvent::class, function ($e) {
            return $e->project->title === 'Test' ;
        });
    }

    public function testDestroyProject()
    {
        $project = factory(Project::class)->create();
        $projectResponse = $this->actingAs($this->user)->delete("rest/project/" . $project->id);
        $projectResponse->assertStatus(403);

        $this->user->givePermissionTo('edit projects');
        $projectResponse = $this->actingAs($this->user)->delete("rest/project/" . $project->id);
        $projectResponse->assertStatus(200);
    }

    public function testIndex()
    {
        factory(Project::class)->create();
        $projectResponse = $this->actingAs($this->user)->get("/");
        $projectResponse->assertStatus(200);
    }

    public function testDuplicateProject()
    {
        $project = factory(Project::class)->create();

        $admin = factory(User::class)->create();
        $admin->assignRole(Roles::ADMIN);

        $student = factory(User::class)->create();
        $student->assignRole(Roles::STUDENT);

        $projectsResponse = $this->actingAs($student)->get("rest/project/" . $project->id . "/duplicate");
        $projectsResponse->assertStatus(403);

        $projectsResponse = $this->actingAs($admin)->get("rest/project/" . $project->id . "/duplicate");
        $projectsResponse->assertStatus(200);

        Event::assertDispatched(NewProjectEvent::class, function ($e) use ($project) {
            return $e->project->description === $project->description ;
        });
    }

    public function testToggleWatchProject()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();

        $projectsResponse = $this->actingAs($user)->get("rest/project/" . $project->id . "/toggleWatch");
        $projectsResponse->assertStatus(200);
    }
}
