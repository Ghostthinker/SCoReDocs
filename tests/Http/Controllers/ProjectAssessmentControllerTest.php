<?php

namespace Tests\Http\Controllers;

use App\Rules\PermissionSet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAssessmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testGetAssessmentDocs() {
        $projectResponse = $this->actingAs($this->user)->get("rest/assessment-overview-data");
        $projectResponse->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::CAN_VIEW_ASSESSMENT_DOCS_OVERVIEW);
        $projectResponse = $this->actingAs($this->user)->get("rest/assessment-overview-data");
        $projectResponse->assertStatus(200);
    }
}
