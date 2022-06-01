<?php

namespace Tests\Feature;

use App\Enums\MediaStatus;
use App\Repositories\MediaRepositoryInterface;
use App\Services\EvoliService;
use App\Services\UploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EvoliServiceTest extends TestCase
{

    use RefreshDatabase;

    private $mediaRepositoryInterface;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaRepositoryInterface = $this->app->make(MediaRepositoryInterface::class);
    }


    public function testEvoliTokenCache()
    {
        $evoliService = new EvoliService();

        $token = $evoliService->getAccessToken();

        $this->assertNotEmpty($token, 'the  access token should be set');

        //testing if we get the same token

        $token2 = $evoliService->getAccessToken();

        $this->assertEquals($token, $token2);

        //CHECK: Explicit check a new refresh token
        $token3 = $evoliService->getAccessToken(true);

        $this->assertNotEmpty($token3, 'the  access token should be set');

        $this->assertNotEquals($token, $token3);



    }

    /**
     * Integration test the real evoli backend
     *
     * @param EvoliService $evoliService
     * @param UploadService $uploadService
     * @return void
     */
    public function testEvoliSendFile()
    {

        $uploadService = new UploadService($this->mediaRepositoryInterface);

        $file = UploadedFile::fake()
            ->createWithContent('test.mp4',
                file_get_contents(base_path().'/tests/test.mp4'));

        $attributes = [
            'type' => 0,
            'caption' => 'caption',
            'options' => []
        ];
        $media = $uploadService->storeFile($file, $attributes);

        $this->assertNotEmpty($media->previewURL);

        $this->assertNotEmpty($media->streamingId);

        $this->assertEquals(MediaStatus::PENDING, $media->status);
    }
}
