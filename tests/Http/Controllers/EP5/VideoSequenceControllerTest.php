<?php

namespace Tests\Http\Controllers\EP5;

use App\Jobs\ProcessXapi;
use App\Models\EP5\VideoSequence;
use App\Models\Media;
use App\Repositories\EP5\PlaybackCommandRepository;
use App\Repositories\EP5\VideoSequenceRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VideoSequenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function testStore()
    {
        $media = factory(Media::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(
            "rest/ep5/sequences/{$media->id}",
            [
                'duration' => 100,
                'timestamp' => 100,
                'title' => 'Title',
                'description' => 'Descrption',
                'video_nid' => $media->id,
                'user_id' => $user->id,
            ],
            ['accept' => 'application/json']
        );
        $response->assertStatus(201);
    }

    public function testIndex()
    {
        $media = factory(Media::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get("rest/ep5/sequences/{$media->id}");
        $response->assertStatus(200);


        $media2 = factory(Media::class)->create();
        factory(VideoSequence::class, 4)->create([
            'video_nid' => $media->id
        ]);
        factory(VideoSequence::class, 3)->create([
            'video_nid' => $media2->id
        ]);

        $response = $this->actingAs($user)->getJson("rest/ep5/sequences/{$media->id}");

        $responseArray = json_decode($response->getContent(), true);
        $this->assertCount(4, $responseArray);
    }

    public function testDestroy()
    {
        $videoSequenceRepository = new VideoSequenceRepository();
        $media = factory(Media::class)->create();
        $user = factory(User::class)->create();
        $videoSequence = $videoSequenceRepository->create([
            'id' => 1,
            'video_nid' => $media->id,
            'duration' => 100,
            'timestamp' => 100,
            'title' => 'Title',
            'user_id' => $user->id,
            'description' => 'Description'
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->delete("rest/ep5/sequences/{$media->id}/{$videoSequence->id}");
        $response->assertStatus(200);
    }

    public function testUpdatePlaybackCommand()
    {
        $playbackCommandRepository = new PlaybackCommandRepository();
        $media = factory(Media::class)->create();
        $playbackCommand = $playbackCommandRepository->create([
            'id' => 1,
            'video_nid' => $media->id,
            'duration' => 100,
            'timestamp' => 100,
            'title' => 'Title',
            'type' => 'Type',
            'additional_fields' => 'Json'
        ]);
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->json('PUT', "rest/ep5/playbackcommands/{$media->id}/{$playbackCommand->id}", $playbackCommand->toArray());
        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }
}
