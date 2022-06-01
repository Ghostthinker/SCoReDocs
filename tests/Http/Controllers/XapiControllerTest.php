<?php

namespace Tests\Http\Controllers;

use App\Jobs\ProcessXapi;
use App\Models\EP5\Annotation;
use App\Models\Project;
use App\Models\Section;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class XapiControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $project;
    private $section;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->user = factory(User::class)->create();
        $this->be($this->user);
        $this->project = factory(Project::class)->create();
        $this->section = factory(Section::class)->create();
    }

    public function testLeftProject()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/leftproject');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testStartedEditing()
    {

        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/startedediting');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testCanceledEditing()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/canceledediting');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testRevertedVersion()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/revertedversion/123');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testComparedVersions()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/comparedversions/123/123');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testViewedHistory()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/viewedhistory');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testViewed()
    {
        $sectionResponse = $this->get('rest/xapi/projects/' . $this->project->id . '/sections/' . $this->section->id . '/viewed');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testInsertedAnnotation()
    {
        $annotation = factory(Annotation::class)->create();
        $sectionResponse = $this->get('rest/xapi/annotation/' . $annotation->id . '/insert');
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }
}
