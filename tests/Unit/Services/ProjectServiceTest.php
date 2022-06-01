<?php

namespace Tests\Unit\Services;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\SectionStatus;
use App\Mail\AssessmentReadyForReview;
use App\Mail\AssessmentReviewed;
use App\Mail\ProjectArchive;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Section;
use App\Repositories\ProjectRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UserRepository;
use App\Rules\Roles;
use App\Services\ProjectService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    private $sectionRepository;
    private $projectRepository;
    private $userRepository;
    private $projectService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionRepository = $this->app->make(SectionRepository::class);
        $this->projectRepository = $this->app->make(ProjectRepository::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->projectService = new ProjectService($this->sectionRepository, $this->projectRepository, $this->userRepository);

        // Mock mails
        Mail::fake();
    }

    public function testUpdateAssessmentDocStatus() {
        $project = factory(Project::class)->create([
            'status' => ProjectStatus::IN_PROGRESS,
            'type' => ProjectType::ASSESSMENT_DOC
        ]);
        $section1 = factory(Section::class)->create([
            'status' => SectionStatus::IN_PROGRESS,
            'index' => 1,
            'project_id' => $project->id
        ]);
        $section2 = factory(Section::class)->create([
            'status' => SectionStatus::IN_PROGRESS,
            'index' => 2,
            'project_id' => $project->id
        ]);
        factory(Section::class)->create([
            'status' => SectionStatus::EDIT_NOT_POSSIBLE,
            'index' => 3,
            'project_id' => $project->id
        ]);
        factory(Section::class)->create([
            'status' => SectionStatus::EDIT_NOT_POSSIBLE,
            'index' => 4,
            'project_id' => $project->id
        ]);
        $projectStatus = $this->projectService->updateAssessmentDocStatus($project, SectionStatus::IN_PROGRESS, SectionStatus::SUBMITTED);
        $project = $this->projectRepository->get($project->id);
        $this->assertEquals(ProjectStatus::IN_PROGRESS, $project->status, 'Project Status sollte immer noch "In Progress" sein');
        $this->assertEquals(ProjectStatus::IN_PROGRESS, $projectStatus, 'Rückgabewert sollte "In Progress" sein');

        $this->sectionRepository->update($section1, ['status' => SectionStatus::SUBMITTED]);
        $this->sectionRepository->update($section2, ['status' => SectionStatus::SUBMITTED]);
        $projectStatus = $this->projectService->updateAssessmentDocStatus($project, SectionStatus::IN_PROGRESS, SectionStatus::IN_PROGRESS);
        $project = $this->projectRepository->get($project->id);
        $this->assertEquals(ProjectStatus::IN_PROGRESS, $project->status, 'Project Status sollte immer noch "In Progress" sein');
        $this->assertEquals(ProjectStatus::IN_PROGRESS, $projectStatus, 'Rückgabewert sollte "In Progress" sein');

        $projectStatus = $this->projectService->updateAssessmentDocStatus($project, SectionStatus::IN_PROGRESS, SectionStatus::SUBMITTED);
        $project = $this->projectRepository->get($project->id);
        $this->assertEquals(ProjectStatus::SUBMITTED, $project->status, 'Project Status sollte jetzt "Submitted" sein');
        $this->assertEquals(ProjectStatus::SUBMITTED, $projectStatus, 'Rückgabewert sollte "Submitted" sein');
    }

    public function testSendAssessmentDocStatusChangeMails() {
        $profile = factory(Profile::class)->create();
        $assUser = $profile->user;
        $assUser->assignRole(Roles::STUDENT);
        $project = factory(Project::class)->create([
            'status' => ProjectStatus::IN_PROGRESS,
            'type' => ProjectType::ASSESSMENT_DOC,
            'assessment_doc_owner_id' => $assUser->id
        ]);
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user1->assignRole(Roles::ADVISOR);
        $user2->assignRole(Roles::ADVISOR);
        $advisors = $this->userRepository->getUsersWithRoleAdvisor();
        $this->projectService->sendAssessmentDocStatusChangeMails(ProjectStatus::IN_PROGRESS, ProjectStatus::IN_PROGRESS, $project);
        Mail::assertNothingSent();

        $this->projectService->sendAssessmentDocStatusChangeMails(ProjectStatus::IN_PROGRESS, ProjectStatus::SUBMITTED, $project);
        foreach ($advisors as $advisor) {
            Mail::assertSent(AssessmentReadyForReview::class, function ($mail) use ($advisor) {
                return $mail->hasTo($advisor->email);
            });
        }

        $this->projectService->sendAssessmentDocStatusChangeMails(ProjectStatus::IN_PROGRESS, ProjectStatus::COMPLETED, $project);
        Mail::assertSent(AssessmentReviewed::class, function ($mail) use ($assUser) {
            return $mail->hasTo($assUser->email);
        });
    }

    public function testSendMailProjectIsArchived() {
        $profile = factory(Profile::class)->create();
        $assUser = $profile->user;
        $assUser->assignRole(Roles::STUDENT);
        /* @var Project $project */
        $project = factory(Project::class)->create([
            'status' => ProjectStatus::IN_PROGRESS,
            'type' => ProjectType::PROJECT,
            'assessment_doc_owner_id' => $assUser->id
        ]);
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user1->assignRole(Roles::ADVISOR);
        $user2->assignRole(Roles::ADVISOR);
        $project->user_involve()->save($assUser);
        $project->user_involve()->save($user1);
        $this->projectService->sendMailProjectIsArchived($project);
        Mail::assertNothingSent();

        $project->type = ProjectType::ARCHIVED;
        $project->save();

        $users = $project->user_involve()->get();
        $this->projectService->sendMailProjectIsArchived($project);
        foreach ($users as $user) {
            Mail::assertQueued(ProjectArchive::class, function ($mail) use ($user) {
                return $mail->hasTo($user->email);
            });
        }

        $this->projectService->sendMailProjectIsArchived($project);
        Mail::assertNotQueued(ProjectArchive::class, function ($mail) use ($user2) {
            return $mail->hasTo($user2->email);
        });
    }
}
