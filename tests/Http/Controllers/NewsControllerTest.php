<?php

namespace Tests\Http\Controllers;

use App\Events\NewNewsEvent;
use App\Events\UpdateNewsEvent;
use App\Models\News;
use App\Rules\PermissionSet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Event;

class NewsControllerTest extends TestCase {

    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
        $this->user = factory(User::class)->create();
    }


    public function testStore() {
        $response = $this->actingAs($this->user)->post("rest/news",
            [
                'title' => 'Title',
                'content' => 'Beschreibung'
            ]
        );
        $response->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::CREATE_NEWS);
        $response = $this->actingAs($this->user)->post("rest/news",
            [
                'title' => 'Title',
                'content' => 'Beschreibung'
            ]
        );
        $response->assertStatus(201);
        $this->assertEquals('Title', $response->getOriginalContent()->title);
        $this->assertEquals('Beschreibung', $response->getOriginalContent()->content);

        Event::assertDispatched(NewNewsEvent::class);
    }

    public function testIndex() {
        $news = factory(News::class)->create();
        $response = $this->actingAs($this->user)->get("rest/news");
        $response->assertStatus(200);

        $this->assertEquals(1, sizeof($response->getOriginalContent()['news']), 'There should be one entry in the response array');
        $this->assertEquals($news->id, $response->getOriginalContent()['news'][0]->id);
        $this->assertEquals($news->title, $response->getOriginalContent()['news'][0]->title);
        $this->assertEquals($news->content, $response->getOriginalContent()['news'][0]->content);
        $this->assertEquals(false, $response->getOriginalContent()['can_edit_news']);
        $this->assertEquals(false, $response->getOriginalContent()['can_create_news']);

        $this->user->givePermissionTo(PermissionSet::CREATE_NEWS);
        $response = $this->actingAs($this->user)->get("rest/news");
        $response->assertStatus(200);
        $this->assertEquals(true, $response->getOriginalContent()['can_create_news']);
        $this->assertEquals(false, $response->getOriginalContent()['can_edit_news']);

        $this->user->revokePermissionTo(PermissionSet::CREATE_NEWS);
        $this->user->givePermissionTo(PermissionSet::EDIT_NEWS);
        $response = $this->actingAs($this->user)->get("rest/news");
        $response->assertStatus(200);
        $this->assertEquals(false, $response->getOriginalContent()['can_create_news']);
        $this->assertEquals(true, $response->getOriginalContent()['can_edit_news']);

        $this->user->givePermissionTo(PermissionSet::CREATE_NEWS);
        $response = $this->actingAs($this->user)->get("rest/news");
        $response->assertStatus(200);
        $this->assertEquals(true, $response->getOriginalContent()['can_create_news']);
        $this->assertEquals(true, $response->getOriginalContent()['can_edit_news']);
    }

    public function testUpdate() {
        $news = factory(News::class)->create();
        $news->title = 'Neuer Titel';
        $news->content = 'Neue Beschreibung';

        $response = $this->actingAs($this->user)->put("rest/news",
            $news->toArray()
        );
        $response->assertStatus(403);
        $this->user->givePermissionTo(PermissionSet::EDIT_NEWS);
        $response = $this->actingAs($this->user)->put("rest/news",
            $news->toArray()
        );
        $response->assertStatus(200);


        $this->assertEquals('Neuer Titel', $response->getOriginalContent()->title);
        $this->assertEquals('Neue Beschreibung', $response->getOriginalContent()->content);

        Event::assertDispatched(UpdateNewsEvent::class);
    }

    public function testRead() {
        $news = factory(News::class)->create();
        $this->assertEquals(false,  (bool) $news->usersRead()->where('user_id', $this->user->id)->first(), 'Read news should not be set.');

        $response = $this->actingAs($this->user)->post("rest/news/".$news->id."/read");
        $response->assertStatus(200);

        $this->assertEquals(true,  (bool) $news->usersRead()->where('user_id', $this->user->id)->first(), 'Read news should be set after reading news.');
    }

    public function testResetReadAfterUpdate() {
        $news = factory(News::class)->create();
        $news->title = 'Neuer Titel';
        $news->content = 'Neue Beschreibung';
        $this->user->givePermissionTo(PermissionSet::EDIT_NEWS);

        $this->assertEquals(false,  (bool) $news->usersRead()->where('user_id', $this->user->id)->first());

        $response = $this->actingAs($this->user)->post("rest/news/".$news->id."/read");
        $response->assertStatus(200);

        $this->assertEquals(true,  (bool) $news->usersRead()->where('user_id', $this->user->id)->first());


        $response = $this->actingAs($this->user)->put("rest/news",
            $news->toArray()
        );
        $response->assertStatus(200);

        $this->assertEquals(false,  (bool) $news->usersRead()->where('user_id', $this->user->id)->first(), 'Read news should be reset after updating news.');
    }
}
