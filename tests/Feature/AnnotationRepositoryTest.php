<?php

namespace Tests\Feature;

use App\Models\EP5\Annotation;
use App\Models\Media;
use App\Repositories\EP5\AnnotationRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnotationRepositoryTest extends TestCase
{
    use RefreshDatabase;


    public function testCRUD()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $annotation = $annotationRepository->create([
            'body' => '',
            'timestamp' => 0,
            'video_nid' => $media->id
        ]);

        $this->assertNotEmpty($annotation->id);

        //retrieve all
        $all = $annotationRepository->all();

        $this->assertEquals(1, count($all));
    }

    public function testGetByMediaId()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $media2 = factory(Media::class)->create();

        factory(Annotation::class, 4)->create([
            'video_nid' => $media->id
        ]);

        factory(Annotation::class, 2)->create([
            'video_nid' => $media2->id
        ]);


        $annotations = $annotationRepository->getByMediaId($media->id);

        $this->assertCount(4, $annotations);

    }

    public function testGetByMediaIdWithReplies()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $media2 = factory(Media::class)->create();

        $annotation = factory(Annotation::class)->create([
            'video_nid' => $media->id,
        ]);

        factory(Annotation::class)->create([
            'video_nid' => $media->id,
            'parent_id' => $annotation->id
        ]);

        $annotations = $annotationRepository->getByMediaId($media->id);
        $this->assertCount(1, $annotations);
        $this->assertEquals($annotation->id, $annotations->first()->id);

        $replies = $annotations->first()->replies;
        $this->assertCount(1, $replies);
    }

    public function testAddReply()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $annotation = factory(Annotation::class)->create([
            'video_nid' => $media->id
        ]);

        $user2 = factory(User::class)->create();
        //add a reply
        $replyData = [
            'body' => "test 234",
            'user_id' => $user2->id,
            'parent_id' => $annotation->id
        ];

        $annotationReply = $annotationRepository->addReply($replyData);

        $this->assertEquals($annotation->id, $annotationReply->parent_id);
        $this->assertEquals($annotation->timestamp, $annotationReply->timestamp);
        $this->assertEquals($replyData['body'], $annotationReply->body);
        $this->assertEquals($user2->id, $annotationReply->user_id);


    }

    public function testUpdate()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $annotation = factory(Annotation::class)->create([
            'video_nid' => $media->id
        ]);


        $updatedData = ['body' => 'Lorem ipsum dolor sit amet'];

        $success = $annotationRepository->update($annotation->id, $updatedData);

        // annotation object returned here
        $this->assertNotFalse($success);

        $annotation = $annotationRepository->get($annotation->id);
        $this->assertEquals($updatedData['body'], $annotation->body);

    }

    public function testRevisioning()
    {
        $annotationRepository = new AnnotationRepository();

        $media = factory(Media::class)->create();

        $annotation = $annotationRepository->create([
            'body' => 'Lorem',
            'timestamp' => 0,
            'video_nid' => $media->id
        ]);

        $this->assertNotEmpty($annotation->id);

        $annotationLoaded = $annotationRepository->get($annotation->id);

        $this->assertNotEmpty($annotationLoaded->id);

        $this->assertCount(1, $annotationLoaded->audits);

        $annotationLoaded->audits()->first();

        $annotationLoaded->update([
            'body' => 'lorem2'
        ]);

        $annotationLoaded = $annotationRepository->get($annotation->id);
        $this->assertCount(2, $annotationLoaded->audits);


        $revision2 = $annotationLoaded->audits()->find(2);

        $modified = $revision2->getModified();

        $this->assertEquals('lorem2', $modified['body']['new']);
        $this->assertEquals('Lorem', $modified['body']['old']);
    }
}
