<?php

namespace Tests\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\MessageMentionings;
use App\Models\Project;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
    }

    public function testSendMessage()
    {
        Event::fake();
        $project = factory(Project::class)->create();
        $user = factory(User::class)->create();
        $message = $this->actingAs($user)->post("rest/message/send/".$project->id, [
            'data' => ['text' => 'Test'],
            'type' => 'text',
            'mentionings' => []
        ]);
        $message->assertStatus(201);

        $messageArray = json_decode($message->content(), true);
        Event::assertDispatched(function (MessageEvent $messageEvent) use ($messageArray) {
            return $messageEvent->getMessage()->user_id === $messageArray['author'];
        });
    }

    public function testGetMessagesForProject()
    {
        $user = factory(User::class)->create();
        $messageResponse = $this->actingAs($user)->get("rest/messages/get/0");
        $messageResponse->assertStatus(200);
    }

    public function testGetMessagesForContext()
    {
        $user = factory(User::class)->create();
        $messageResponse = $this->actingAs($user)->get("rest/messages/get/0/0");
        $messageResponse->assertStatus(200);
    }

    public function testResourceTransformation()
    {
        $message = factory(Message::class)->state('text')->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->getJson("rest/messages/get/" . $message->project);
        $responseArray = json_decode($response->getContent(), true);
        $messageArray = $responseArray[0];

        $this->assertArrayHasKey('author', $messageArray);
        $this->assertArrayHasKey('type', $messageArray);
        $this->assertArrayHasKey('text', $messageArray['data']);
        $this->assertArrayHasKey('meta', $messageArray['data']);

        $message = factory(Message::class)->state('text')->create(['user_id' => $user->id, 'project' => $message->project]);

        $response = $this->actingAs($user)->getJson("rest/messages/get/" . $message->project);
        $responseArray = json_decode($response->getContent(), true);
        $messageArray = $responseArray[1];

        $this->assertArrayHasKey('author', $messageArray);
        $this->assertEquals('me', $messageArray['author']);
        $this->assertIsNumeric($responseArray[0]['author']);
    }

    public function testMarkMessageMentioningAsRead()
    {
        $user = factory(User::class)->create();
        $messageMentioning = factory(MessageMentionings::class)->create(
            [
                'user_id' => $user->id
            ]);
        $response = $this->actingAs($user)->get("rest/messages/mentioning/".$messageMentioning->id."/markAsRead");
        $response->assertStatus(200);
    }

    public function testGetMessageMentioningsForProject()
    {
        $user = factory(User::class)->create();
        $messageMentioning = factory(MessageMentionings::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("rest/project/".$messageMentioning->project_id."/mentionings");
        $response->assertStatus(200);
    }
}
