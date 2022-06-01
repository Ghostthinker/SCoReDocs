<?php

namespace Tests\Unit\Services;

use App\Models\LockTimeout;
use App\Models\Section;
use App\Repositories\SectionRepository;
use App\Services\TimeoutService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeoutServiceTest extends TestCase
{

    use RefreshDatabase;

    private $service;
    private $sectionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(TimeoutService::class);
        $this->sectionRepository = $this->app->make(SectionRepository::class);
    }

    public function testUnlockTimedOutSections()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();
        $section->locked = true;
        $section->locking_user = $user->id;
        $section->save();
        self::assertTrue(boolval($section->locked), 'Section should be locked at test start.');
        // Check exceeded timeout
        $timeout = factory(LockTimeout::class)->create();
        $timeout->updated_at = strtotime("+15 minutes");
        $timeout->section_id = $section->id;
        $timeout->save(['timestamps' => false]);
        $this->service->unlockTimedOutSections();
        $section = $this->sectionRepository->get($section->id)->first();
        self::assertFalse(boolval($section->locked), 'Section should be unlocked because timeout exceeded 12min.');
        // Check  not exceeded timeout
        $section = factory(Section::class)->create();
        $section->locked = true;
        $section->locking_user = $user->id;
        $section->save();
        $timeout = factory(LockTimeout::class)->create();
        $timeout->section_id = $section->id;
        $timeout->save();
        self::assertTrue(boolval($section->locked), 'Section should be locked because timeout did not exceeded 10min.');
    }

    public function testTimeoutExceedsMinutes()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->updated_at = strtotime("+12 minutes");
        $timeout->save(['timestamps' => false]);
        $this->assertTrue($this->service->timeoutExceedsMinutes($timeout, 10), 'Timeout exceeds the 10 minutes');
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $this->assertFalse($this->service->timeoutExceedsMinutes($timeout, 10),
            'Timeout does not exceed the 10 minutes');
    }
}
