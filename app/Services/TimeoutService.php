<?php

namespace App\Services;

use App\Events\SectionLockEvent;
use App\Models\LockTimeout;
use App\Repositories\SectionRepositoryInterface;
use App\Repositories\TimeoutRepositoryInterface;
use Illuminate\Support\Carbon;

class TimeoutService
{
    public const TIMEOUT_MINUTES = 12;
    private $timeoutRepository;
    private $sectionRepository;
    private $sectionService;

    public function __construct(
        TimeoutRepositoryInterface $timeoutRepository,
        SectionRepositoryInterface $sectionRepository,
        SectionService $sectionService
    ) {
        $this->timeoutRepository = $timeoutRepository;
        $this->sectionRepository = $sectionRepository;
        $this->sectionService = $sectionService;
    }

    public function unlockTimedOutSections()
    {
        $timedOutSections = [];
        $timeouts = $this->timeoutRepository->all();
        foreach ($timeouts as $timeout) {
            if ($this->timeoutExceedsMinutes($timeout, self::TIMEOUT_MINUTES)) {
                $this->sectionService->unLockSection($timeout->section_id);
                $section = $this->sectionRepository->get($timeout->section_id);
                $timedOutSections[] = $section;
                broadcast(new SectionLockEvent($timeout->section_id, $section->project_id));
                $this->timeoutRepository->delete($timeout->id);
            }
        }
        return $timedOutSections;
    }

    public function timeoutExceedsMinutes(LockTimeout $timeout, int $minutes)
    {
        $exceedMinutes = Carbon::now()->diffInMinutes($timeout->updated_at);
        return $exceedMinutes >= $minutes;
    }
}
