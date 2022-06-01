<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\SectionStatus;
use App\Mail\AssessmentReadyForReview;
use App\Mail\AssessmentReviewed;
use App\Mail\ProjectArchive;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\SectionRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;

/**
 * ProjectService
 *
 * @package App\Services
 */
class ProjectService
{
    private $sectionRepository;
    private $projectRepository;
    private $userRepository;

    public function __construct(
        SectionRepositoryInterface $sectionRepository,
        ProjectRepositoryInterface $projectRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->sectionRepository = $sectionRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Checks if project has a new status and returns status
     *
     * @param $project
     * @param $oldStatus
     * @param $newStatus
     * @return int
     */
    public function updateAssessmentDocStatus($project, $oldStatus, $newStatus)
    {
        if ($oldStatus === $newStatus) {
            return $project->status;
        }

        $sections = $this->sectionRepository->getAllByProjectId($project->id)->sortBy('index');
        $status = array_column($sections->toArray(), 'status');
        // Ignore last two sections (only used for drafts)
        array_splice($status, count($status) - 2, 2);
        if (count(array_count_values($status)) == 1) {
            $data = [];
            switch ($status[0]) {
                case SectionStatus::IN_PROGRESS:
                    $data = ['status' => ProjectStatus::IN_PROGRESS];
                    break;
                case SectionStatus::SUBMITTED:
                    $data = ['status' => ProjectStatus::SUBMITTED];
                    break;
                case SectionStatus::IN_REVIEW:
                    $data = ['status' => ProjectStatus::IN_REVIEW];
                    break;
                case SectionStatus::COMPLETED:
                    $data = ['status' => ProjectStatus::COMPLETED];
                    break;
            }
            $this->projectRepository->update($project->id, $data);
            return $data['status'];
        }
        return $project->status;
    }

    /**
     * Sends mails when doc status has changed
     *
     * @param int $oldStatus
     * @param int $newStatus
     * @param Project $project
     */
    public function sendAssessmentDocStatusChangeMails(int $oldStatus, int $newStatus, Project $project)
    {
        if ($oldStatus == $newStatus) {
            return;
        }

        $student = $project->assessmentDocOwner;
        switch ($newStatus) {
            case SectionStatus::SUBMITTED:
                $advisors = $this->userRepository->getUsersWithRoleAdvisor();
                foreach ($advisors as $advisor) {
                    $mailData = [];
                    $mailData['student_name'] = $student->name;
                    $mailData['matriculation_number'] = $student->profile->matriculation_number ? $student->profile->matriculation_number : 'nicht angegeben';
                    $mailData['linkToAssessment'] = url("/project/{$project->id}");
                    $mailData['advisor_name'] = $advisor->name;
                    Mail::to($advisor->email)->send(new AssessmentReadyForReview($mailData));
                }
                break;
            case SectionStatus::COMPLETED:
                $mailData = [];
                $mailData['name'] = $student->name;
                $mailData['projectId'] = $project->id;
                Mail::to($student->email)->send(new AssessmentReviewed($mailData));
        }
    }

    public function sendMailProjectIsArchived(Project $project) {
        if ($project->type !== ProjectType::ARCHIVED) { return false; }

        $users = $project->user_involve()->get();
        foreach ($users as $user) {
            $mailData = [];
            $mailData['name'] = $user->getNameAttribute();
            $mailData['projectId'] = $project->id;
            $mailData['projectTitle'] = $project->title;
            $mailData['updateDate'] = $project->updated_at;
            Mail::to($user)->send(new ProjectArchive($mailData));
        }
        return true;
    }
}
