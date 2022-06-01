<?php

namespace Tests\Feature;

use App\Enums\MediaStatus;
use App\Facades\Evoli;
use App\Models\Media;
use App\Repositories\MediaRepositoryInterface;
use App\Services\UploadService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadServiceTest extends TestCase
{
    use RefreshDatabase;

    private $mediaRepositoryInterface;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaRepositoryInterface = $this->app->make(MediaRepositoryInterface::class);
        Queue::fake();
    }

    public function testStoreFile()
    {
        $uploadService = new UploadService($this->mediaRepositoryInterface);

        Evoli::shouldReceive('transferMedia')
            ->once()
            ->andReturn([
                'id' => '12345',
                'convertedURL_720p' => 'https://example.com/stream',
                'convertedURL_1080p' => 'https://example.com/stream',
                'convertedURL_2160p' => 'https://example.com/stream',
                'previewURL' => 'https://example.com/preview',
            ]);

        Evoli::shouldReceive('getMediaInformation')
            ->twice()
            ->andReturn([
                'status' => 'converting'
            ]);

        //only partial mock, only the  two functions above
        Evoli::makePartial();

        $file = UploadedFile::fake()
            ->createWithContent('test.mp4',
                file_get_contents(base_path().'/tests/test.mp4'));

        $attributes = [
            'type' => 0,
            'caption' => 'caption'
        ];
        $media = $uploadService->storeFile($file, $attributes);

        $this->assertEquals(MediaStatus::PENDING, $media->status);

        $this->assertNotEmpty($media->streamingId);

        $mediaInformation = Evoli::getMediaInformation($media);
        $this->assertEquals('converting', $mediaInformation['status']);
    }
}
