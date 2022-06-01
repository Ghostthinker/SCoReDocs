<?php

namespace Tests\Unit\Services;

use App\Enums\SectionStatus;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Section;
use App\Repositories\ActivityRepository;
use App\Repositories\AuditRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UserRepository;
use App\Rules\PermissionSet;
use App\Services\ActivityService;
use App\Services\SectionService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\TestCase;

class SectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private $sectionRepository;
    private $auditRepository;
    private $userRepository;
    private $activityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->auditRepository = $this->app->make(AuditRepository::class);
        $this->sectionRepository = $this->app->make(SectionRepository::class);
        $activityRepository = $this->app->make(ActivityRepository::class);
        $this->activityService = new ActivityService($activityRepository);
    }

    private function createSection($heading, $index, $projectId, $status)
    {
        $sectionRepository = new SectionRepository();
        return $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => $heading,
            'index' => $index,
            'project_id' => $projectId,
            'locked' => false,
            'status' => $status,
        ]);
    }

    public function testStoreSection()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $section = $sectionService->storeSection($project->id, ['topSectionId' => 0]);

        $section1Fresh = $this->sectionRepository->get($section->id);

        $this->assertEquals(0, $section1Fresh->index);
    }

    public function testStoreSectionIndex()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $section1 = $this->createSection(1, 0, $project->id, SectionStatus::EDIT_NOT_POSSIBLE);
        $section2 = $this->createSection(1, 1, $project->id, SectionStatus::EDIT_NOT_POSSIBLE);

        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $sectionService->storeSection($project->id, [
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'topSectionId' => 1,
            'project_id' => $project->id,
            'locked' => false
        ]);

        $section2Fresh = $this->sectionRepository->get($section2->id);
        $section1Fresh = $this->sectionRepository->get($section1->id);

        $this->assertEquals(2, $section2Fresh->index);
        $this->assertEquals(0, $section1Fresh->index);
        $this->assertEquals(1, Section::all()->last()->index);
    }

    public function testStoreSectionId()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $section1 = $this->createSection(1, 1, $project->id, SectionStatus::EDIT_NOT_POSSIBLE);
        $section2 = $this->createSection(1, 2, $project->id, SectionStatus::EDIT_NOT_POSSIBLE);

        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $indexIgnore = 20;
        $sectionService->storeSection($project->id, [
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => $indexIgnore,
            'topSectionId' => $section1->id,
            'project_id' => $project->id,
            'locked' => false
        ]);

        $section2Fresh = $this->sectionRepository->get($section2->id);
        $section1Fresh = $this->sectionRepository->get($section1->id);

        $this->assertEquals(3, $section2Fresh->index);
        $this->assertEquals(1, $section1Fresh->index);
        $this->assertNotEquals($indexIgnore, Section::all()->last()->index);
        $this->assertEquals(2, Section::all()->last()->index);
    }

    public function testUpdateSection()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $section = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);

        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $success = $sectionService->updateSection($section->id, [
            'title' => 'Neuer Titel',
            'changeLog' => 'Wilde Anpassungen'
        ]);
        $this->assertEquals(1, $success);
        $this->assertCount(1, Activity::all());
        $this->assertEquals($section->id, Activity::first()->section_id);

        // test submit with child not set
        $section2 = $this->createSection(2, 2, $project->id, SectionStatus::IN_PROGRESS);
        $section3 = $this->createSection(3, 3, $project->id, SectionStatus::SUBMITTED);
        $sectionFresh2 = $this->sectionRepository->get($section2->id);
        $sectionFresh3 = $this->sectionRepository->get($section3->id);
        $this->assertEquals(SectionStatus::IN_PROGRESS, $sectionFresh2->status);
        $this->assertEquals(SectionStatus::SUBMITTED, $sectionFresh3->status);

        $success = $sectionService->updateSection($section3->id, ['status' => SectionStatus::SUBMITTED, 'changeLog' => 'update status']);
        $this->assertTrue($success);

        $success = $sectionService->updateSection($section2->id, ['status' => SectionStatus::SUBMITTED, 'changeLog' => 'update status']);
        $this->assertTrue($success);

        $success = $sectionService->updateSection($section->id, ['status' => SectionStatus::SUBMITTED, 'changeLog' => 'update status']);
        $this->assertTrue($success);

        // test status update with parent status update
        $success = $sectionService->updateSection($section3->id, ['status' => SectionStatus::IN_PROGRESS, 'changeLog' => 'update status']);
        $this->assertTrue($success);

        $sectionFresh3 = $this->sectionRepository->get($section3->id);
        $sectionFresh2 = $this->sectionRepository->get($section2->id);
        $sectionFresh = $this->sectionRepository->get($section->id);

        $this->assertEquals(SectionStatus::IN_PROGRESS, $sectionFresh3->status);
        $this->assertEquals(SectionStatus::IN_PROGRESS, $sectionFresh2->status);
        $this->assertEquals(SectionStatus::IN_PROGRESS, $sectionFresh->status);
    }

    public function testUpdateSectionStatusChange()
    {
        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);
        $user->assignRole('SCoRe-Team');

        $section1 = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);
        $section2 = $this->createSection(2, 2, $project->id, SectionStatus::IN_PROGRESS);
        $section3 = $this->createSection(3, 3, $project->id, SectionStatus::IN_PROGRESS);
        $section4 = $this->createSection(1, 4, $project->id, SectionStatus::IN_PROGRESS);

        $success = $sectionService->updateSection($section1->id, ['status' => SectionStatus::EDIT_NOT_POSSIBLE]);
        $this->assertTrue($success);

        $section1 = $this->sectionRepository->get($section1->id);
        $section2 = $this->sectionRepository->get($section2->id);
        $section3 = $this->sectionRepository->get($section3->id);
        $section4 = $this->sectionRepository->get($section4->id);

        $this->assertEquals(SectionStatus::EDIT_NOT_POSSIBLE, $section1->status);
        $this->assertEquals(SectionStatus::EDIT_NOT_POSSIBLE, $section2->status);
        $this->assertEquals(SectionStatus::EDIT_NOT_POSSIBLE, $section3->status);
        $this->assertEquals(SectionStatus::IN_PROGRESS, $section4->status);

        $this->assertEquals(0, $section1->locked);
        $this->assertEquals(0, $section2->locked);
        $this->assertEquals(0, $section3->locked);
    }

    public function testLockSection()
    {
        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);

        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();

        $section = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);
        $sectionService->lockSection($section->id, $user->id);

        $sectionFresh = $this->sectionRepository->get($section->id);
        $this->assertEquals(1, $sectionFresh->locked);
        $this->assertNotEmpty($sectionFresh->locked_at);
        $this->assertNotEmpty($sectionFresh->locking_user);
        $this->assertCount(1, $sectionFresh->audits->all());

        $user1 = factory(User::class)->create();
        $this->expectException(AccessDeniedHttpException::class);
        $sectionService->lockSection($section->id, $user1->id);
    }

    public function testUnLockSection()
    {
        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);

        $project = factory(Project::class)->create();
        $section = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);

        $sectionService->unlockSection($section->id, 1);

        $sectionFresh = $this->sectionRepository->get($section->id);
        $this->assertEquals(0, $sectionFresh->locked);
        $this->assertEmpty($sectionFresh->locked_at);
        $this->assertEmpty($sectionFresh->locking_user);
        $this->assertCount(1, $sectionFresh->audits->all());
    }

    public function testGetPossibleSectionStatus()
    {
        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $user = factory(User::class)->create();
        $this->be($user);
        $user->givePermissionTo(PermissionSet::SET_STATUS_SUBMITTED);
        $user->givePermissionTo(PermissionSet::CHANGE_STATUS_IN_PROGRESS);

        $project = factory(Project::class)->create();
        $section = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);
        $statusCollection = $sectionService->getPossibleSectionStatus($section->id);
        $statusCollection->each(function ($item) {
            if ($item['status'] == SectionStatus::SUBMITTED) {
                $this->assertTrue($item['allowed'], 'User should be able to set Submitted after gaining rights.');
            } elseif ($item['status'] == SectionStatus::IN_PROGRESS) {
                $this->assertTrue($item['allowed'], 'Status changes to same status are always allowed because nothing changes.');
            } else {
                $this->assertFalse($item['allowed'], 'User should not be able to set Status ' . SectionStatus::getDescription($item['status']) . ' without Permission.');
            }
        });
    }

    public function testDeleteSection()
    {
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $this->be($user);

        $section = $this->createSection(1, 1, $project->id, SectionStatus::IN_PROGRESS);

        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);
        $success = $sectionService->destroySection($section->id, []);
        $this->assertFalse($success, 'Missing changelog');

        $success = $sectionService->destroySection($section->id, ['changeLog' => 'Wird nicht mehr benötigt']);
        $this->assertTrue($success, 'Data array needed');

        $section = Section::onlyTrashed()->find($section->id);
        $this->assertNotNull($section, 'Section is trash');
        $this->assertNotNull($section->deleted_at, 'Timestamp is set on deleted_at');
    }

    public function testUpdateSectionWithIndex()
    {
        $sectionService = new SectionService($this->sectionRepository, $this->userRepository, $this->auditRepository, $this->activityService);

        $section = factory(Section::class)->create();
        $section2 = $this->createSection(1, 0, $section->project_id, SectionStatus::IN_PROGRESS);
        $data = [];
        $data['topSectionId'] = $section->id;
        $sectionService->updateSectionWithIndex($section2, $data);
        $section2Updated = $this->sectionRepository->get($section2->id);
        $this->assertEquals($section->index + 1, $section2Updated->index);
    }
}
