<?php

namespace Tests\Feature;

use App\Enums\MediaStatus;
use App\Models\Media;
use App\Repositories\MediaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MediaRepository();
    }

    public function testGetMediaByStatus()
    {
        factory(Media::class)->create();
        factory(Media::class)->create();
        factory(Media::class)->create();
        $media = factory(Media::class)->make();
        $media->status = MediaStatus::PENDING;
        $media->save();
        $media = factory(Media::class)->make();
        $media->status = MediaStatus::PENDING;
        $media->save();

        $result = $this->repository->getMediaByStatus(MediaStatus::PENDING);
        self::assertEquals(2, count($result), 'There should be two matches for pending media.');
    }
}
