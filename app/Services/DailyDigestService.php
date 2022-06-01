<?php

namespace App\Services;

use App\Mail\DailyDigest;
use App\Models\Project;
use App\Repositories\DailyDigestRepositoryInterface;
use App\User;
use Illuminate\Support\Facades\Mail;

class DailyDigestService
{

    private $dailyDigestRepository;

    public function __construct(DailyDigestRepositoryInterface $repository)
    {
        $this->dailyDigestRepository = $repository;
    }

    public function createDigest($startDate, $endDate)
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $usersWatchProject = $project->user_watch;
            $dailyDigestByProject = $this->createDigestForProject($project, $startDate, $endDate);


            $hasDigest = array_filter(array_values($dailyDigestByProject), function ($n) {
                return ($n > 0);
            });


            foreach ($usersWatchProject as $user) {
                $dailyDigestForInvolvedSections = [];
                if (!empty($hasDigest)) {
                    $dailyDigestForInvolvedSections = $this->createDigestForInvolvedSections($project, $user->id,
                        $startDate, $endDate);
                }
                $dailyDigestForMentioning = $this->createDigestForMentioning($user->id, $startDate, $endDate);
                $dailyDigestsForAtAllMentioning = $this->createDigestForAtAllMentioning($project, $user->id);

                if($dailyDigestForMentioning > 0 || count($dailyDigestsForAtAllMentioning) > 0 || !empty($hasDigest)){
                    $this->sendDailyDigestMail(
                        $user,
                        $project,
                        $dailyDigestByProject,
                        $dailyDigestForInvolvedSections,
                        $dailyDigestForMentioning,
                        $dailyDigestsForAtAllMentioning
                    );
                }
            }
        }
    }

    private function sendDailyDigestMail(
        User $user,
        Project $project,
        $dailyDigest,
        $dailyDigestForInvolvedSections = [],
        $dailyDigestForMentioning = [],
        $dailyDigestsForAtAllMentioning = []
    ) {
        $mailData = [];
        $mailData['name'] = $user->name;
        $mailData['projectId'] = $project->id;
        $mailData['projectTitle'] = $project->title;
        $mailData['dailyDigest'] = $dailyDigest;
        $mailData['text'] = $this->createMailText($dailyDigest, $dailyDigestForInvolvedSections, $dailyDigestForMentioning, $dailyDigestsForAtAllMentioning, $project);
        Mail::to($user->email)->send(new DailyDigest($mailData));
    }

    private function createMailText($dailyDigest, $dailyDigestForInvolvedSections, $dailyDigestForMentioning, $dailyDigestsForAtAllMentioning, $project)
    {
        $mailText = '';
        $firstElementAmount = 0;
        $mailTextBlocks = [];

        if(count($dailyDigestsForAtAllMentioning) > 0) {
            if(count($dailyDigestsForAtAllMentioning) > 1){
                $mailText .= 'At Alle Benachrichtigungen: <br>';
            }else{
                $mailText .= 'At Alle Benachrichtigung: <br>';
            }
            foreach ($dailyDigestsForAtAllMentioning as $atAllMentioning) {
                $redirect = url('/project/'. $atAllMentioning->message->project);
                //(and) => &; XapiController will replace (and) to &. Had to mask '&' otherwise the redirect path would miss the query params; also (hash) => #
                $redirect .= '?messageId=' . $atAllMentioning->message->id . '(and)mentioningId=' . $atAllMentioning->id;
                $sectionId = $atAllMentioning->message->section_id;
                $redirect .= $sectionId ? '(hash)Section-' . $sectionId : '';
                $mailText .= '<a href="'.url('/xapi/link/atAllLink?redirectPath=' . $redirect . '&projectId=' . $project->id . '&sectionId=' . $sectionId . '').'" target="_blank">' . $atAllMentioning->message->text . '</a> <br>';
            }
        }
        if ($dailyDigest['createdSectionAmount'] > 1) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdSectionAmount'].'</b> neue Abschnitte angelegt');
            $firstElementAmount = $dailyDigest['createdSectionAmount'];
        } elseif ($dailyDigest['createdSectionAmount'] > 0) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdSectionAmount'].'</b> neuer Abschnitt angelegt');
            $firstElementAmount = $dailyDigest['createdSectionAmount'];
        }
        if ($dailyDigest['changedSectionAmount'] > 1) {
            array_push($mailTextBlocks,
                '<b>'.$dailyDigest['changedSectionAmount'].'</b> Änderungen an bestehenden Abschnitten vorgenommen');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['changedSectionAmount'];
            }
        } elseif ($dailyDigest['changedSectionAmount'] > 0) {
            array_push($mailTextBlocks,
                '<b>'.$dailyDigest['changedSectionAmount'].'</b> Änderung an einem bestehenden Abschnitt vorgenommen');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['changedSectionAmount'];
            }
        }
        if ($dailyDigest['createdVideoAmount'] > 1) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdVideoAmount'].'</b> neue Videos hochgeladen');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['createdVideoAmount'];
            }
        } elseif ($dailyDigest['createdVideoAmount'] > 0) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdVideoAmount'].'</b> neues Video hochgeladen');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['createdVideoAmount'];
            }
        }
        if ($dailyDigest['createdAnnotationAmount'] > 1) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdAnnotationAmount'].'</b> Videokommentare gemacht');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['createdAnnotationAmount'];
            }
        } elseif ($dailyDigest['createdAnnotationAmount'] > 0) {
            array_push($mailTextBlocks, '<b>'.$dailyDigest['createdAnnotationAmount'].'</b> Videokommentar gemacht');
            if ($firstElementAmount < 1) {
                $firstElementAmount = $dailyDigest['createdAnnotationAmount'];
            }
        }

        if ($firstElementAmount > 1) {
            $mailText .= 'Es wurden ';
        } elseif ($firstElementAmount > 0){
            $mailText .= 'Es wurde ';
        }

        foreach ($mailTextBlocks as $index => $mailTextBlock) {
            if (sizeof($mailTextBlocks) > 1 && $index === sizeof($mailTextBlocks) - 2) {
                $mailText .= $mailTextBlock.' und ';
            } elseif ($index !== sizeof($mailTextBlocks) - 1) {
                $mailText .= $mailTextBlock.', ';
            } else {
                $mailText .= $mailTextBlock;
            }
        }

        if (!empty($dailyDigestForInvolvedSections)) {
            $mailText .= '<br><br>Folgende Abschnitte an denen Du aktiv beteiligt bist, wurden verändert:';

            foreach ($dailyDigestForInvolvedSections as $dailyDigestForInvolvedSection) {
                $mailText .= '<br>"'.$dailyDigestForInvolvedSection['section']->title.'" wurde <b>'.$dailyDigestForInvolvedSection['count'].'</b> mal bearbeitet.';
                $sectionUrl = url('/project/'.$dailyDigestForInvolvedSection['project']->id.'#Section-'.$dailyDigestForInvolvedSection['section']->id);
                $mailText .= '<br> Weitere Infos am <a href="'.$sectionUrl.'" target="_blank">Abschnitt</a> via Chat.';
            }
        }

        if (!empty($dailyDigestForMentioning)) {
            if ($dailyDigestForMentioning == 1) {
                $mailText .= '<br><br>Chat Erwähnungen:<br>Du wurdest in einem Projekt erwähnt.<br>';
            } else {
                $mailText .= '<br><br>Chat Erwähnungen:<br>Du wurdest in '.$dailyDigestForMentioning.' Projekten erwähnt.<br>';
            }
            $mailText .= '<a href="'.url('/xapi/link/mentioningLink?redirectPath=/&projectId='.$project->id.'').'" target="_blank">Sieh dir die Nachrichten jetzt an!</a>';
        }

        return $mailText;
    }

    private function createDigestForProject(Project $project, $startDate, $endDate)
    {

        $createdSectionAmount = $this->dailyDigestRepository->getNewCreatedSectionAmount($project, $startDate,
            $endDate);
        $changedSectionAmount = $this->dailyDigestRepository->getChangedSectionsAmount($project, $startDate, $endDate);
        $createdVideoAmount = $this->dailyDigestRepository->getNewCreatedVideoAmount($project, $startDate, $endDate);
        $createdAnnotationAmount = $this->dailyDigestRepository->getNewCreatedAnnotationAmount($project, $startDate,
            $endDate);

        return [
            'createdSectionAmount' => $createdSectionAmount,
            'changedSectionAmount' => $changedSectionAmount,
            'createdVideoAmount' => $createdVideoAmount,
            'createdAnnotationAmount' => $createdAnnotationAmount
        ];

    }

    private function createDigestForInvolvedSections(Project $project, $userId, $startDate, $endDate)
    {
        return $this->dailyDigestRepository->getInvolvedSectionChangesAmount($project, $userId, $startDate, $endDate);
    }

    private function createDigestForMentioning($userId, $startDate, $endDate)
    {
        return $this->dailyDigestRepository->getAmountOfProjectsUserWasMentionedFromTo($userId, $startDate, $endDate);
    }

    private function createDigestForAtAllMentioning($project, $userId)
    {
        return $this->dailyDigestRepository->getAtAllMentioningOfProjectByUserId($project, $userId);
    }

}
