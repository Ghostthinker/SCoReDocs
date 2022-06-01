<?php

namespace Tests\Http\Controllers\EP5;

use App\Jobs\ProcessXapi;
use App\Models\EP5\Annotation;
use App\Models\EP5\VideoSequence;
use App\Models\Media;
use App\Rules\Roles;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AnnotationControllerTest extends TestCase
{
    use RefreshDatabase;

    private $userAdmin;
    private $userStudent;
    private $userStudent2;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->userAdmin = factory(User::class)->create();
        $this->userAdmin->assignRole(Roles::ADMIN);

        $this->userStudent = factory(User::class)->create();
        $this->userStudent->assignRole(Roles::STUDENT);

        $this->userStudent2 = factory(User::class)->create();
        $this->userStudent2->assignRole(Roles::STUDENT);

        $this->be($this->userStudent);
    }

    public function testStore()
    {
        $media = factory(Media::class)->create();
        $sequence = factory(VideoSequence::class)->create();

        $response = $this->post("rest/ep5/comments/{$media->id}",
            [
                'body' => 'test',
                'timestamp' => 0,
                'video_nid' => $media->id,
                'sequence_id' => $sequence->id,
            ],
            ['accept' => 'application/json']
        );

        $response->assertStatus(201);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testUpdate()
    {

        $annotation = factory(Annotation::class)->create();
        $response = $this->actingAs($this->userStudent)->put("rest/ep5/comments/{$annotation->video_nid}/{$annotation->id}",
            [
                'body' => 'test123',
                'timestamp' => 0,
                'video_nid' => $annotation->video_nid
            ]
        );

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testDestroy()
    {
        $annotation = factory(Annotation::class)->create();
        $response = $this->actingAs($this->userStudent)->delete("rest/ep5/comments/" . $annotation->video_nid . '/' . $annotation->id);

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testPostReply()
    {
        $media = factory(Media::class)->create();
        $annotation = factory(Annotation::class)->create([
            'video_nid' => $media->id
        ]);

        $response = $this->actingAs($this->userStudent)->post("rest/ep5/comments/{$media->id}/reply/{$annotation->id}",
            [
                'body' => 'test',
                'timestamp' => 0
            ],
            ['accept' => 'application/json']
        );

        $response->assertStatus(201);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testPutReply()
    {
        $media = factory(Media::class)->create();

        $annotation = factory(Annotation::class)->create([
            'video_nid' => $media->id
        ]);

        $reply = $this->actingAs($this->userStudent)->post("rest/ep5/comments/{$media->id}/reply/{$annotation->id}",
            [
                'body' => 'test',
                'timestamp' => 0
            ],
            ['accept' => 'application/json']
        );

        $reply->assertStatus(201);
        Queue::assertPushed(ProcessXapi::class);

        $response = $this->actingAs($this->userStudent)->put("rest/ep5/comments/{$media->id}/reply/{$annotation->id}/{$reply['id']}",
            [
                'body' => 'test 2',
                'timestamp' => 0
            ],
            ['accept' => 'application/json']
        );

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);
    }

    public function testDestroyReply()
    {
        $annotation = factory(Annotation::class)->create();
        $annotationChild = factory(Annotation::class)->create([
            'parent_id' => $annotation->id
        ]);
        $response = $this->actingAs($this->userStudent)->delete("rest/ep5/comments/{$annotation->video_nid}/reply/{$annotation->id}/{$annotationChild->id}");
        $response->assertStatus(200);

        Queue::assertPushed(ProcessXapi::class);
    }

    public function testPermissionAnnotations()
    {
        $annotation = factory(Annotation::class)->create();
        $response = $this->actingAs($this->userStudent)->put("rest/ep5/comments/{$annotation->video_nid}/{$annotation->id}",
            [
                'body' => 'test123',
                'timestamp' => 0,
                'video_nid' => $annotation->video_nid
            ]
        );

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);

        // check deny access for other student
        // update
        $response = $this->actingAs($this->userStudent2)->put("rest/ep5/comments/{$annotation->video_nid}/{$annotation->id}",
            [
                'body' => 'test321',
                'timestamp' => 0,
                'video_nid' => $annotation->video_nid
            ]
        );

        $response->assertStatus(403);
        Queue::assertPushed(ProcessXapi::class);

        // delete
        $response = $this->actingAs($this->userStudent2)->delete("rest/ep5/comments/" . $annotation->video_nid . '/' . $annotation->id);

        $response->assertStatus(403);
        Queue::assertPushed(ProcessXapi::class);

        // allow owner
        // delete
        $response = $this->actingAs($this->userStudent)->delete("rest/ep5/comments/" . $annotation->video_nid . '/' . $annotation->id);

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);

        // bypass admin
        $annotation2 = factory(Annotation::class)->create();
        $response = $this->actingAs($this->userAdmin)->put("rest/ep5/comments/{$annotation2->video_nid}/{$annotation2->id}",
            [
                'body' => 'testAdmin',
                'timestamp' => 0,
                'video_nid' => $annotation2->video_nid
            ]
        );
        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);

        $response = $this->actingAs($this->userAdmin)->delete("rest/ep5/comments/" . $annotation2->video_nid . '/' . $annotation2->id);

        $response->assertStatus(200);
        Queue::assertPushed(ProcessXapi::class);


    }
}
