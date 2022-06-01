<?php

namespace Tests\Feature;

use App\Models\EP5\PlaybackCommand;
use App\Models\Media;
use App\Repositories\EP5\PlaybackCommandRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaybackCommandRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $media = factory(Media::class)->create();

        $playbackCommand = factory(PlaybackCommand::class)->state('zoom')->create([
            'video_nid' => $media->id
        ]);

        $this->assertEquals('zoom', $playbackCommand->type);
        $this->assertCount(6, $playbackCommand->additional_fields['zoom_transform']);
    }

    /**
     * Basic CRUD test
     */
    public function testCRUD()
    {
        $playbackCommandRepository = new PlaybackCommandRepository();
        $media = factory(Media::class)->create();
        $user  = factory(User::class)->create();
        $playbackCommand = $playbackCommandRepository->create([
            'video_nid' => $media->id,
            'duration' => 100,
            'timestamp' => 100,
            'title' => 'Title',
            'type' => 'Type',
        ], $user);

        $this->assertNotEmpty($playbackCommand->id);
        $this->assertNotEmpty($playbackCommand->duration);
        $this->assertNotEmpty($playbackCommand->timestamp);
        $this->assertNotEmpty($playbackCommand->title);
        $this->assertNotEmpty($playbackCommand->type);
        $this->assertNotEmpty($playbackCommand->video_nid);

        $pbUser = $playbackCommand->user;
        $this->assertEquals($user->id, $pbUser->id);

        //retrieve all
        $all = $playbackCommandRepository->all();
        $this->assertCount(1, $all);

        $one = $playbackCommandRepository->get($playbackCommand->id);
        $this->assertEquals($playbackCommand->id, $one->id);

        $newTitle = ['title' => 'Neuer Title'];

        $status = $playbackCommandRepository->update($playbackCommand->id, $newTitle);
        $this->assertTrue($status);

        $updatedPlaybackCommand = $playbackCommandRepository->get($playbackCommand->id);

        $this->assertEquals($newTitle['title'], $updatedPlaybackCommand->title);

        $playbackCommandRepository->delete($playbackCommand->id);
        $this->assertCount(0, $playbackCommandRepository->all());
    }

    public function testGetByMediaId()
    {
        $playbackCommandRepository = new PlaybackCommandRepository();

        $media = factory(Media::class)->create();

        $media2 = factory(Media::class)->create();

        factory(PlaybackCommand::class, 4)->create([
            'video_nid' => $media->id
        ]);

        factory(PlaybackCommand::class, 2)->create([
            'video_nid' => $media2->id
        ]);

        $playbackCommands = $playbackCommandRepository->getByMediaId($media->id);
        $this->assertCount(4, $playbackCommands);
    }

    public function testRevisioning()
    {
        $playbackCommandRepository = new PlaybackCommandRepository();

        $playbackCommand = factory(PlaybackCommand::class)->create([
            'title' => 'Test'
        ]);

        $this->assertNotEmpty($playbackCommand->id);

        $playbackCommandLoaded = $playbackCommandRepository->get($playbackCommand->id);

        $this->assertNotEmpty($playbackCommandLoaded->id);

        $this->assertCount(1, $playbackCommandLoaded->audits);

        $playbackCommandLoaded->update([
            'title' => 'Neuer Test'
        ]);

        $playbackCommandLoaded = $playbackCommandRepository->get($playbackCommand->id);
        $this->assertCount(2, $playbackCommandLoaded->audits);


        $revision2 = $playbackCommandLoaded->audits()->find(2);
        $modified = $revision2->getModified();

        $this->assertEquals('Neuer Test', $modified['title']['new']);
        $this->assertEquals('Test', $modified['title']['old']);
    }
}
