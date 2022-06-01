<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Project;
use App\Models\Section;
use App\Repositories\MessageReadsRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageReadsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testGetCountOfUnreadSectionMessagesByProjectAndUser()
    {
        $messageReadsRepository = new MessageReadsRepository();

        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create(['project_id' => $project->id]);
        $message = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section->id
        ]);

        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages->count(),
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });
        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 1);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], $message->id);

        // Project Message should not be regarded
        $message1 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages->count(),
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });
        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 1);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], $message->id);

        // We Expect a new $unreadMessageCount for the new section
        $section2 = factory(Section::class)->create(['project_id' => $project->id]);
        $message2 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section2->id,
        ]);
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages->count(),
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });

        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 1);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], $message->id);
        $this->assertEquals($unreadMessageCount[1]['section_id'], $section2->id);
        $this->assertEquals($unreadMessageCount[1]['messages_count'], 1);
        $this->assertEquals($unreadMessageCount[1]['firstUnreadMessageId'], $message2->id);

        // We expect the count of the first section to increase by 1
        $message3 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section->id
        ]);
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages->count(),
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });
        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 2);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], $message->id);
    }

    public function testGetCountOfUnreadProjectMessagesByProjectAndUser()
    {
        $messageReadsRepository = new MessageReadsRepository();

        $project = factory(Project::class)->create();
        $message = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);

        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($projectMessageCount, 1);

        $section = factory(Section::class)->create(['project_id' => $project->id]);
        $message2 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section->id
        ]);
        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($projectMessageCount, 1);

        $message3 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);
        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($projectMessageCount, 2);
    }

    public function testGetFirstUnreadProjectMessageByProjectAndUser()
    {
        $messageReadsRepository = new MessageReadsRepository();

        $project = factory(Project::class)->create();
        $message = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);

        $firstUnreadProjectMessage = $messageReadsRepository->getFirstUnreadProjectMessageByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($firstUnreadProjectMessage->id, $message->id);

        $section = factory(Section::class)->create(['project_id' => $project->id]);
        $message2 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section->id
        ]);
        $firstUnreadProjectMessage = $messageReadsRepository->getFirstUnreadProjectMessageByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($firstUnreadProjectMessage->id, $message->id);

        $message3 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);
        $firstUnreadProjectMessage = $messageReadsRepository->getFirstUnreadProjectMessageByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($firstUnreadProjectMessage->id, $message->id);
    }

    public function testMarkAllSectionMessagesAsRead() {
        $messageReadsRepository = new MessageReadsRepository();

        $project = factory(Project::class)->create();
        factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => null
        ]);

        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($projectMessageCount, 1);

        $messageReadsRepository->markAllSectionMessagesAsRead($project->id, $this->user->id);
        $projectMessageCount = $messageReadsRepository->getCountOfUnreadProjectMessagesByProjectAndUser($project->id, $this->user->id);
        $this->assertEquals($projectMessageCount, 1);

        $section = factory(Section::class)->create(['project_id' => $project->id]);
        $message1 = factory(Message::class)->create([
            'project' => $project->id,
            'section_id' => $section->id
        ]);
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages->count(),
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });
        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 1);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], $message1->id);

        $messageReadsRepository->markAllSectionMessagesAsRead($project->id, $this->user->id);
        $project = $messageReadsRepository->getCountOfUnreadSectionMessagesByProjectAndUser($project->id, $this->user->id);
        $unreadMessageCount = $project->sections->map(function ($item) {
            return [
                "section_id" => $item->id,
                "messages_count" => $item->messages_count,
                "firstUnreadMessageId" => $item->messages->isNotEmpty() ? $item->messages->first()->id : null
            ];
        });
        $this->assertEquals($unreadMessageCount[0]['section_id'], $section->id);
        $this->assertEquals($unreadMessageCount[0]['messages_count'], 0);
        $this->assertEquals($unreadMessageCount[0]['firstUnreadMessageId'], null);
    }
}
