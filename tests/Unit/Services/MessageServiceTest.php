<?php


namespace Tests\Unit\Services;


use App\Events\MessageMentionEvent;
use App\Jobs\ProcessXapi;
use App\Models\Message;
use App\Models\MessageMentionings;
use App\Models\Project;
use App\Repositories\MessageMentioningsRepository;
use App\Repositories\MessageRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UserRepository;
use App\Rules\Roles;
use App\Services\MessageService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    private $messageService;
    private $messageMentioningsRepository;
    private $projectRepository;
    private $sectionRepository;
    private $userRepository;
    private $messageRepository;


    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Queue::fake();

        $this->sectionRepository = $this->app->make(SectionRepository::class);
        $this->projectRepository = $this->app->make(ProjectRepository::class);
        $this->userRepository = $this->app->make(UserRepository::class);
        $this->messageRepository = $this->app->make(MessageRepository::class);
        $this->messageMentioningsRepository = $this->app->make(MessageMentioningsRepository::class);

        $this->messageService = new MessageService(
            $this->messageMentioningsRepository,
            $this->sectionRepository,
            $this->projectRepository,
            $this->userRepository,
            $this->messageRepository
        );
    }

    public function testGetParticipantsByProject()
    {
        $project = factory(Project::class)->create();

        $userStudent = factory(User::class)->create();
        $userStudent2 = factory(User::class)->create();
        $userStudent3 = factory(User::class)->create();
        $userAdmin = factory(User::class)->create();
        $userTeam = factory(User::class)->create();

        $userStudent->assignRole(Roles::STUDENT);
        $userStudent2->assignRole(Roles::STUDENT);
        $userStudent3->assignRole(Roles::STUDENT);
        $userAdmin->assignRole(Roles::ADMIN);
        $userTeam->assignRole(Roles::TEAM);

        $participants1 = $this->messageService->getParticipantsByProject($project)->filter(function ($value, $key) {
            return $value->showUserInParticipantList == true;
        });
        $this->assertEquals(2, count($participants1), 'Es sollten 2 Participants enthalten sein');

        //Student watch Project
        $userStudent->project_watch()->toggle($project->id);

        $participants2 = $this->messageService->getParticipantsByProject($project)->filter(function ($value, $key) {
            return $value->showUserInParticipantList == true;
        });
        $this->assertEquals(3, count($participants2), 'Es sollten 3 Participants enthalten sein');

        //simulate that student changed something in project
        $userStudent2->project_involve()->syncWithoutDetaching($project->id);

        $participants3 = $this->messageService->getParticipantsByProject($project)->filter(function ($value, $key) {
            return $value->showUserInParticipantList == true;
        });
        $this->assertEquals(4, count($participants3), 'Es sollten 4 Participants enthalten sein');

        //create Message in Project from Student, so User gets involved in project
        $message = factory(Message::class)->create([
            'user_id' => $userStudent3->id,
            'project' => $project->id
        ]);
        $participants4 = $this->messageService->getParticipantsByProject($project)->filter(function ($value, $key) {
            return $value->showUserInParticipantList == true;
        });
        $this->assertEquals(5, count($participants4), 'Es sollten 5 Participants enthalten sein');

    }

    public function testCreateMessageMentionings()
    {
        $message = factory(Message::class)->create();
        $this->be(User::find($message->user_id));
        $user = factory(User::class)->create();

        $request = new class {
            public function fullUrl(){
                return "Hello, this function is added at runtime";
            }
        };
        $request->mentionings = [$user->id];

        $messageMentioningsArray = $this->messageService->createMessageMentionings($request, $message->id, null, $message->project);

        $this->assertTrue(sizeof($messageMentioningsArray) == 1);
        $this->assertEquals($user->id, $messageMentioningsArray[0]->user_id);

        Event::assertDispatched(function (MessageMentionEvent $event) use ($message) {
            return $event->projectId === $message->project;
        });
    }

    public function testCreateAtAllMentioning() {
        $user = factory(User::class)->create();
        $this->be($user);
        $message = factory(Message::class)->create();

        $request = new class {
            public function fullUrl(){
                return "Hello, this function is added at runtime";
            }
        };

        $this->messageService->createAtAllMentioning($request,$message->id,null,$message->project);
        Event::assertDispatched(function (MessageMentionEvent $event) use ($message) {
            return $event->messageId === $message->id;
        });
        Queue::assertPushed(ProcessXapi::class);

        // Only the author of the message should get mentioned (because there are no other users)
        $messageMentioning = MessageMentionings::where('user_id', $message->user_id)->get();
        $this->assertEquals(1,sizeof($messageMentioning));
    }
}
