<?php

namespace Tests\Feature;

use App\Models\LockTimeout;
use App\Models\Section;
use App\Repositories\TimeoutRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeoutsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TimeoutRepository();
    }

    public function testDeleteBySectionId()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $sectionId = $timeout->section_id;
        $this->repository->deleteBySectionId($sectionId);
        $this->assertEmpty(LockTimeout::all(), 'Timeouts should be empty after delete.');
    }

    public function testDelete()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $this->repository->delete($timeout->id);
        $this->assertEmpty(LockTimeout::all(), 'Timeouts should be empty after delete.');
    }

    public function testAll()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $timeouts = $this->repository->all();
        $this->assertEquals(2, $timeouts->count());
    }

    public function testGetBySectionId()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $sectionId = $timeout->section_id;
        $this->assertNotEmpty($this->repository->getBySectionId($sectionId), 'Timeout must be there');
    }

    public function testGet()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $rexeivedTimeout = $this->repository->get($timeout->id);
        $this->assertNotEmpty($rexeivedTimeout, 'Timeout must be there');
    }

    public function testUpdate()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $section = factory(Section::class)->create();
        $section->save();
        $this->repository->update($timeout, ['section_id' => $section->id]);
        $this->assertNotEmpty($this->repository->getBySectionId($section->id),
            'Section must be found by updated section id');
    }

    public function testCreateBySectionId()
    {
        $section = factory(Section::class)->create();
        $section->save();
        $timeout = $this->repository->createBySectionId($section->id);
        $this->assertNotEmpty($timeout, 'Timeout should be able to be created by sectionId.');
    }

    public function testUpdateTimeout()
    {
        $timeout = factory(LockTimeout::class)->create();
        $timeout->save();
        $old_updated_at = $timeout->updated_at;
        sleep(1);
        $this->repository->updateTimeout($timeout->section_id);
        $timeout = $this->repository->getBySectionId($timeout->section_id);
        $new_updated_at = $timeout->updated_at;
        $this->assertGreaterThan($old_updated_at, $new_updated_at);
    }

    public function testCreate()
    {
        $section = factory(Section::class)->create();
        $section->save();
        $this->assertNotEmpty($this->repository->create(['section_id' => $section->id]), 'Timeout should be created');
    }
}
