<?php

namespace Tests\Http\Controllers\EP5;

use App\Jobs\ProcessXapi;
use App\Models\EP5\PlaybackCommand;
use App\Models\Media;
use App\Repositories\EP5\PlaybackCommandRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use function MongoDB\BSON\toJSON;

class PlaybackCommandControllerTest extends TestCase
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

        $playbackCommand = $this->actingAs($user)->post("rest/ep5/playbackcommands/{$media->id}",
            [
                'duration' => 100,
                'timestamp' => 100,
                'title' => 'Title',
                'type' => 'Type',
                'video_nid' => $media->id,
                'user_id' => $user->id,
                'this_is_additional' => 'So much info'
            ],
            ['accept' => 'application/json']
        );
        $playbackCommand->assertStatus(201);
    }

    public function testIndex()
    {
        $media = factory(Media::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get("rest/ep5/playbackcommands/{$media->id}");
        $response->assertStatus(200);


        $media2 = factory(Media::class)->create();
        factory(PlaybackCommand::class, 4)->create([
            'video_nid' => $media->id
        ]);
        factory(PlaybackCommand::class, 3)->create([
            'video_nid' => $media2->id
        ]);

        $response = $this->actingAs($user)->getJson("rest/ep5/playbackcommands/{$media->id}");

        $responseArray = json_decode($response->getContent(), true);
        $this->assertCount(4, $responseArray);
    }

    public function testResourceTransformation()
    {
        $media = factory(Media::class)->create();

        $createdAt = '11.06.1990 10:11';
        factory(PlaybackCommand::class)->state('zoom')->create([
            'video_nid' => $media->id,
            'created_at' => Carbon::parse($createdAt)
        ]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->getJson("rest/ep5/playbackcommands/{$media->id}");
        $responseArray = json_decode($response->getContent(), true);
        $playbackCommandArray = $responseArray[0];

        $this->assertEquals($createdAt, str_replace('-', ' ', str_replace(' ', '', $playbackCommandArray['date_formatted'])));
        $this->assertArrayHasKey('id', $playbackCommandArray);
        $this->assertArrayHasKey('type', $playbackCommandArray);
        $this->assertArrayHasKey('zoom_transform', $playbackCommandArray);
    }

    public function testDeletePlaybackCommand()
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

        $response = $this->actingAs($user)->delete("rest/ep5/playbackcommands/{$media->id}/{$playbackCommand->id}");
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
