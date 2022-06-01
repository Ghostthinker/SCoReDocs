<?php

namespace Tests\Unit\Services;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Repositories\ProjectRepository;
use App\Services\Xapi\XapiService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;
use TinCan\LRSResponse;
use TinCan\RemoteLRS;
use TinCan\Statement;

class XapiServiceTest extends TestCase
{
    use RefreshDatabase;

    private $projectRepository;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->user = factory(User::class)->create();
        $this->be($this->user);
        $this->projectRepository = $this->app->make(ProjectRepository::class);
    }

    public function testSendStatement()
    {
        // Mocking response
        $response = new LRSResponse(true, null, null);
        $response->success = true;

        $lrsMock = Mockery::mock(RemoteLRS::class)->makePartial();
        $lrsMock->shouldReceive('saveStatement')->andReturn($response);
        $ser = new XapiService($this->projectRepository);
        $ser->lrs = $lrsMock;
        $res = $ser->sendStatement(new Statement());
        $lrsMock->shouldHaveReceived('saveStatement')->once();
        $this->assertTrue($res->success);
    }

    public function testCreateStatement()
    {
        $activityId = 'www.google.de/123';
        $statement = Xapi::createStatement(
            new XapiVerb(XapiVerb::OPENED),
            new XapiActivityType(XapiActivityType::PROJECT),
            new XapiActivityDescription(XapiActivityDescription::PROJECT_LEFT),
            $activityId,
            ['eu' => 'opened']
        );
        $this->assertNotEmpty($statement->getId(), 'ID should not be empty');

        $this->assertEquals(XapiVerb::getDescription(XapiVerb::OPENED), $statement->getVerb()->getId(),
            'Verb Id is not matching');
        $this->assertNotEmpty($statement->getVerb()->getDisplay(), 'Verb display name should not be empty');

        $this->assertEquals($activityId, $statement->getObject()->getId());
        $this->assertEquals(XapiActivityType::getActivityType(XapiActivityType::PROJECT),
            $statement->getObject()->getDefinition()->getType());
        $this->assertNotEmpty($statement->getObject()->getDefinition()->getDescription());
        $this->assertNotEmpty($statement->getObject()->getDefinition()->getName());
    }

    public function testGetStatements()
    {
        // Mocking response
        $response = new LRSResponse(true, null, null);
        $response->success = true;

        $lrsMock = Mockery::mock(RemoteLRS::class)->makePartial();
        $lrsMock->shouldReceive('queryStatements')->andReturn($response);

        $xapiService = new XapiService($this->projectRepository);
        $xapiService->lrs = $lrsMock;

        $res = $xapiService->getStatements(null, null, 1000);
        $lrsMock->shouldHaveReceived('queryStatements')->once();
        $this->assertTrue($res->success);
    }
}
