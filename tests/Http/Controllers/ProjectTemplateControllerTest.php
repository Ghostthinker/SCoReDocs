<?php

namespace Tests\Http\Controllers;

use App\Enums\ProjectType;
use App\Rules\PermissionSet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testStoreTemplate()
    {
        $projectResponse = $this->actingAs($this->user)->post("rest/templates",
            [
                'title' => 'Title',
                'description' => 'Beschreibung',
                'type' => ProjectType::TEMPLATE
            ]
        );
        $projectResponse->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::EDIT_TEMPLATES);
        $projectResponse = $this->actingAs($this->user)->post("rest/templates",
            [
                'title' => 'Title',
                'description' => 'Beschreibung',
                'type' => ProjectType::TEMPLATE
            ]
        );
        $projectResponse->assertStatus(201);
        $projectResponse = $this->actingAs($this->user)->post("rest/templates",
            [
                'title' => 'Title',
                'description' => 'Beschreibung',
                'type' => ProjectType::TEMPLATE
            ]
        );
        $projectResponse->assertStatus(422);
    }

    public function testGetAssessmentDocTemplate()
    {
        $projectResponse = $this->actingAs($this->user)->get("rest/templates/getAssessmentDocTemplate");
        $projectResponse->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::EDIT_TEMPLATES);
        $projectResponse = $this->actingAs($this->user)->get("rest/templates/getAssessmentDocTemplate");
        $projectResponse->assertStatus(200);
    }

    public function testGetProjectTemplates()
    {
        $projectResponse = $this->actingAs($this->user)->get("rest/templates/getProjectTemplates");
        $projectResponse->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::EDIT_TEMPLATES);
        $projectResponse = $this->actingAs($this->user)->get("rest/templates/getProjectTemplates");
        $projectResponse->assertStatus(200);
    }
}
