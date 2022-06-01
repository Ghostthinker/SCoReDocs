<?php

namespace Tests\Http\Controllers;

use App\Enums\ProjectType;
use App\Enums\SectionStatus;
use App\Events\UserWatchesProjectEvent;
use App\Jobs\ProcessXapi;
use App\Models\Project;
use App\Models\Section;
use App\Repositories\SectionRepository;
use App\Rules\PermissionSet;
use App\Rules\Roles;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->user = factory(User::class)->create();
    }

    public function testStoreSection()
    {
        Event::fake();
        $project = factory(Project::class)->create();
        $sectionResponse = $this->actingAs($this->user)->post("rest/projects/" . $project->id . "/sections/",
            [
                'heading' => 1,
                'index' => 1,
                'locked' => true,
                'locked_at' => now(),
                'locking_user' => $this->user->id,
                'project_id' => $project->id
            ]
        );
        $sectionResponse->assertStatus(200);
        Event::assertDispatched(UserWatchesProjectEvent::class);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testGetSections()
    {
        $project = factory(Project::class)->create();
        factory(Section::class)->create([
            'project_id' => $project->id
        ]);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project->id . "/sections/");
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);

        // User should not have access to sections for a project with type template without permission
        $project_template = factory(Project::class)->create([
            'type' => ProjectType::TEMPLATE
        ]);
        factory(Section::class)->create([
            'project_id' => $project_template->id
        ]);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_template->id . "/sections/");
        $sectionResponse->assertStatus(403);

        // User should not have access to sections for a project with type template without permission
        $project_project_template = factory(Project::class)->create([
            'type' => ProjectType::PROJECT_TEMPLATE
        ]);
        factory(Section::class)->create([
            'project_id' => $project_project_template->id
        ]);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_project_template->id . "/sections/");
        $sectionResponse->assertStatus(403);

        // User should not have access to sections for a project with type template without permission
        $project_archived = factory(Project::class)->create([
            'type' => ProjectType::ARCHIVED
        ]);
        factory(Section::class)->create([
            'project_id' => $project_archived->id
        ]);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_archived->id . "/sections/");
        $sectionResponse->assertStatus(403);

        // User should have access to sections for a project with type template when he has the permission
        $this->user->givePermissionTo(PermissionSet::EDIT_TEMPLATES);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_template->id . "/sections/");
        $sectionResponse->assertStatus(200);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_project_template->id . "/sections/");
        $sectionResponse->assertStatus(200);
        $this->user->givePermissionTo(PermissionSet::CAN_VIEW_ARCHIVE);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $project_archived->id . "/sections/");
        $sectionResponse->assertStatus(200);
    }

    public function testUpdateSection()
    {
        Event::fake();
        $this->user->givePermissionTo(PermissionSet::BREAK_SECTION_WORKFLOW);
        $section = factory(Section::class)->create();
        $repo = new SectionRepository();
        $repo->update($section, ['heading' => 3]);

        $sectionResponse = $this->actingAs($this->user)->put("rest/projects/" . $section->project_id . "/sections/" . $section->id, [
            'title' => 'Test',
            'status' => SectionStatus::IN_PROGRESS
        ]);
        $sectionResponse->assertStatus(200);

        // Checking that heading 1 and 2 can only be set with permission
        $this->actingAs($this->user)->put("rest/projects/" . $section->project_id . "/sections/" . $section->id, [
            'title' => 'Test',
            'heading' => 1,
            'status' => SectionStatus::IN_PROGRESS
        ])->assertStatus(302);

        $user2 = factory(User::class)->create();
        $user2->givePermissionTo(PermissionSet::SET_HEADING_1_TYPE);
        $user2->givePermissionTo(PermissionSet::SET_HEADING_2_TYPE);
        $user2->givePermissionTo(PermissionSet::CHANGE_HEADING_1_CONTENT);
        $user2->givePermissionTo(PermissionSet::CHANGE_HEADING_2_CONTENT);
        $this->actingAs($user2)->put("rest/projects/" . $section->project_id . "/sections/" . $section->id, [
            'title' => 'Test',
            'heading' => 1,
            'status' => SectionStatus::IN_PROGRESS
        ])->assertStatus(200);

        $user3 = factory(User::class)->create();
        $repo->update($section, ['heading' => 3]);
        $repo->update($section, ['status' => SectionStatus::IN_PROGRESS]);
        // Checking that special status changes cannot only be made with permission
        $this->actingAs($user3)->put("rest/projects/" . $section->project_id . "/sections/" . $section->id, [
            'title' => 'Test',
            'status' => SectionStatus::COMPLETED
        ])->assertStatus(302);

        $user4 = factory(User::class)->create();
        $repo->update($section, ['heading' => 3]);
        $repo->update($section, ['status' => SectionStatus::IN_REVIEW]);
        $user4->givePermissionTo(PermissionSet::SET_STATUS_COMPLETED);
        $user4->givePermissionTo(PermissionSet::CHANGE_STATUS_IN_REVIEW);
        $sectionResponse = $this->actingAs($user4)->put("rest/projects/" . $section->project_id . "/sections/" . $section->id, [
            'title' => 'Test',
            'status' => SectionStatus::COMPLETED
        ]);
        $sectionResponse->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
        Event::assertDispatched(UserWatchesProjectEvent::class, 3);
    }

    public function testDestroySectionData()
    {
        $section = factory(Section::class)->create();

        $uri = 'rest/projects/' . $section->project_id . '/sections/';
        $data = ['changeLog' => 'Obsolete'];

        $section = factory(Section::class)->create();
        $user = User::find($section->author_id);
        $sectionResponse = $this->actingAs($user)->delete($uri . $section->id);
        $sectionResponse->assertStatus(304);

        $sectionResponse = $this->actingAs($user)->delete($uri . $section->id, $data);
        $sectionResponse->assertStatus(204);
    }

    public function testDestroySectionLocked()
    {
        $data = ['changeLog' => 'Obsolete'];

        $user = factory(User::class)->create();
        $section = factory(Section::class)->create(['heading' => 1, 'locked' => true]);
        $this->assertEquals($section->heading, 1);
        $this->assertTrue($section->locked);
        $this->assertNotEquals($section->author_id, $user->id);

        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($user)->delete($uri, $data)->assertStatus(403);

        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_ADVISOR);
        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_LOCKED);

        $section = factory(Section::class)->create(['heading' => 3, 'locked' => true]);
        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($user)->delete($uri, $data)->assertStatus(204);

        $section = factory(Section::class)->create(['heading' => 1, 'locked' => true]);
        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($user)->delete($uri, $data)->assertStatus(403);

        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_HEADING_1);
        $user->givePermissionTo(PermissionSet::CAN_DELETE_SECTIONS_HEADING_2);

        $section = factory(Section::class)->create(['heading' => 1, 'locked' => true]);
        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($user)->delete($uri, $data)->assertStatus(204);
    }

    public function testDestroySectionWithRoles()
    {
        $data = ['changeLog' => 'Obsolete'];

        $userAdmin = factory(User::class)->create();
        $userAdmin->assignRole(Roles::ADMIN);

        $section = factory(Section::class)->create(['heading' => 1, 'locked' => true]);
        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($userAdmin)->delete($uri, $data)->assertStatus(204);

        $userTeam = factory(User::class)->create();
        $userTeam->assignRole(Roles::TEAM);
        $section = factory(Section::class)->create(['heading' => 1, 'locked' => false]);
        $uri = 'rest/projects/' . $section->project_id . '/sections/' . $section->id;
        $this->actingAs($userTeam)->delete($uri, $data)->assertStatus(204);
    }

    public function testLock()
    {
        // TODO Check broadcasting
        $section = factory(Section::class)->create(['locked' => true]);
        $sectionResponse = $this->actingAs($this->user)->patch("rest/projects/" . $section->project_id . "/sections/ " . $section->id . "/lock");
        $sectionResponse->assertStatus(200);
    }

    public function testUnlock()
    {
        // TODO Check broadcasting
        $section = factory(Section::class)->create(['locked' => false]);
        $sectionResponse = $this->actingAs($this->user)->patch("rest/projects/" . $section->project_id . "/sections/ " . $section->id . "/unlock");
        $sectionResponse->assertStatus(200);
    }

    public function testGetPossibleSectionStatus()
    {
        $section = factory(Section::class)->create();
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $section->project_id . "/sections/ " . $section->id . "/status");
        $sectionResponse->assertStatus(200);
    }

    public function testGetAudits()
    {
        $section = factory(Section::class)->create();
        $repo = new SectionRepository();
        $repo->update($section, ['heading' => 4]);
        $sectionResponse = $this->actingAs($this->user)->get("rest/projects/" . $section->project_id . "/sections/ " . $section->id . "/audits");
        $sectionResponse->assertStatus(200);
        $content = Json_decode($sectionResponse->getContent());
        $this->assertEquals(2, count($content->audits));
        $this->assertEquals('created', $content->audits[1]->event);
        $this->assertEquals('updated', $content->audits[0]->event);
        $this->assertEquals(4, $content->audits[0]->new_values->heading);
    }

    public function testGetDeletedSections()
    {
        $section = factory(Section::class)->create();
        $user = User::find($section->author_id);
        $data = ['changeLog' => 'Obsolete'];
        $sectionResponse = $this->actingAs($user)->delete('rest/projects/' . $section->project_id . '/sections/' .
            $section->id, $data);
        $sectionResponse->assertStatus(204);
        $deletedSectionResponse = $this->actingAs($user)->get('rest/projects/' . $section->project_id . '/sections/deleted/all');
        $content = Json_decode($deletedSectionResponse->getContent());
        $this->assertEquals(1, $content->total);
    }

    public function testRevertSection()
    {
        $section = factory(Section::class)->create();
        $user = User::find($section->author_id);
        $user->assignRole(Roles::ADMIN);
        $data = ['changeLog' => 'Obsolete'];
        $repo = new SectionRepository();

        $uri = 'rest/projects/' . $section->project_id . '/sections/';

        $sectionResponse = $this->actingAs($user)->delete($uri . $section->id, $data);
        $sectionResponse->assertStatus(204);

        $deletedSectionResponse = $this->actingAs($user)->get($uri . 'deleted/all');
        $content = Json_decode($deletedSectionResponse->getContent());
        $this->assertEquals(1, $content->total);

        $this->actingAs($user)->get($uri . $section->id . '/revert/0');

        $sectionReverted = $repo->get($section->id);
        $this->assertEquals(null, $sectionReverted->deleted_at);

    }

    public function testCreateSectionInAssessment () {
        // create users
        $admin = factory(User::class)->create();
        $admin->assignRole(Roles::ADMIN);

        $scoreTeam = factory(User::class)->create();
        $scoreTeam->assignRole(Roles::TEAM);

        $advisor = factory(User::class)->create();
        $advisor->assignRole(Roles::ADVISOR);

        $student = factory(User::class)->create();
        $student->assignRole(Roles::STUDENT);

        // create assessment doc
        $project = factory(Project::class)->create([
            'type' => ProjectType::ASSESSMENT_DOC
        ]);

        // test as admin
        $sectionResponse = $this->actingAs($admin)->post("rest/projects/" . $project->id . "/sections/",
            [
                'heading' => 1,
                'index' => 1,
                'locked' => false,
            ]
        );
        $sectionResponse->assertStatus(200);

        // test as scoreteam
        $sectionResponse = $this->actingAs($scoreTeam)->post("rest/projects/" . $project->id . "/sections/",
            [
                'heading' => 1,
                'index' => 2,
                'topSectionId' => 1,
                'locked' => false,
            ]
        );
        $sectionResponse->assertStatus(200);

        // test as advisor
        $sectionResponse = $this->actingAs($advisor)->post("rest/projects/" . $project->id . "/sections/",
            [
                'heading' => 1,
                'index' => 3,
                'topSectionId' => 2,
                'locked' => false,
            ]
        );
        $sectionResponse->assertStatus(200);

        // test as score
        $sectionResponse2 = $this->actingAs($student)->post("rest/projects/" . $project->id . "/sections/",
            [
                'heading' => 1,
                'index' => 4,
                'topSectionId' => 3,
                'locked' => false,
            ]
        );
        $sectionResponse2->assertStatus(302);
    }

    public function testOpenSection()
    {
        $admin = factory(User::class)->create();
        $admin->assignRole(Roles::ADMIN);

        $section = factory(Section::class)->create([
            'project_id' => 1
        ]);

        $openedSectionResponse = $this->actingAs($admin)->get('rest/projects/' . $section->project_id . '/sections/' . $section->id . '/open');
        $openedSectionResponse->assertStatus(200);
        $this->assertEquals(0, $admin->section_collapse()->get()->count());
    }

    public function testCloseSection()
    {
        $admin = factory(User::class)->create();
        $admin->assignRole(Roles::ADMIN);

        $section = factory(Section::class)->create([
            'project_id' => 1
        ]);

        $openedSectionResponse = $this->actingAs($admin)->get('rest/projects/' . $section->project_id . '/sections/' . $section->id . '/close');
        $openedSectionResponse->assertStatus(200);
    }
}
