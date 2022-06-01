<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Jobs\ProcessXapi;
use App\Repositories\ProjectRepositoryInterface;
use Auth;
use DateTime;
use Exception;
use Log;
use Str;
use TinCan;
use TinCan\Activity;
use TinCan\ActivityDefinition;
use TinCan\Agent;
use TinCan\Context;
use TinCan\ContextActivities;
use TinCan\LRSResponse;
use TinCan\Statement;
use TinCan\Verb;

class XapiService
{
    private $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->config = config('xapi');
        $this->lrs = new TinCan\RemoteLRS(
            $this->config['api_endpoint'],
            $this->config['version'],
            $this->config['auth']
        );
        $this->statement = new TinCan\Statement();

        $this->projectRepository = $projectRepository;
    }

    /**
     * @param  \App\Enums\XapiVerb  $verb
     * @param  \App\Enums\XapiActivityType  $activityType
     * @param  \App\Enums\XapiActivityDescription  $activityDescription
     * @param  string  $requestUrl
     * @param  array  $activityName
     * @param  array|null  $activityExtension
     * @param  string|null  $contextId
     * @param  array|null  $contextName
     * @param  XapiActivityType|null  $contextActivityType
     * @param  $projectId
     * @return Statement
     */
    public function createStatement(XapiVerb $verb,
                                    XapiActivityType $activityType,
                                    XapiActivityDescription $activityDescription,
                                    string $requestUrl,
                                    array $activityName,
                                    $projectId = null,
                                    ?array $activityExtension = null,
                                    ?string $contextId = null,
                                    ?array $contextName = null,
                                    ?XapiActivityType $contextActivityType = null

    ): Statement {
        $statement = $this->buildStatement(
            $verb,
            $activityType,
            $activityDescription,
            $requestUrl,
            $activityName,
            $projectId,
            $contextId,
            $contextName,
            $contextActivityType,
            $activityExtension
        );
        ProcessXapi::dispatch($statement);
        return $statement;
    }

    /**
     * @param Statement $statement
     * @return TinCan\LRSResponse
     * @throws Exception
     */
    public function sendStatement(Statement $statement): LRSResponse
    {
        $response = $this->lrs->saveStatement($statement);

        if (!$response->success) {
            Log::error('Xapi error', ['response' => $response]);
            throw new Exception('Xapi error: ' . $response->content);
        }

        return $response;
    }

    /**
     * @param  DateTime|null  $from
     * @param  DateTime|null  $until
     * @param  int  $limit
     * @return LRSResponse
     */
    public function getStatements(?string $from, ?string $until, int $limit): LRSResponse
    {
        $query = [
            'limit' => $limit,
            'related_activities' => true,
            'since' => $from, #2017-09-04T12:45:31+00:00
            'until' => $until,
            'ascending' => true,
        ];
        return $this->lrs->queryStatements($query);
    }

    /**
     * Builds a xAPI Statement
     *
     * @param  \App\Enums\XapiVerb  $verb
     * @param  \App\Enums\XapiActivityType  $activityType
     * @param  \App\Enums\XapiActivityDescription  $activityDescription
     * @param  String  $requestUrl
     * @param  $projectId
     * @param  array  $activityName
     * @param  string|null  $contextId
     * @param  array|null  $contextName
     * @param  XapiActivityType|null  $contextActivityType
     * @param  array|null  $activityExtension
     * @return Statement
     */
    private function buildStatement(XapiVerb $verb,
                                    XapiActivityType $activityType,
                                    XapiActivityDescription $activityDescription,
                                    string $requestUrl,
                                    array $activityName,
                                    $projectId = null,
                                    ?string $contextId = null,
                                    ?array $contextName = null,
                                    ?XapiActivityType $contextActivityType = null,
                                    ?array $activityExtension = null): Statement
    {
        $statement = new Statement();

        $activityExtension = $this->addUserRoleToExtension($activityExtension);
        $activityExtension = $this->addProjectDetailsToExtension($activityExtension, $projectId);

        $statement->setVerb($this->buildVerb($verb));
        $statement->setObject($this->buildActivity($requestUrl, $activityType, $activityName, $activityDescription, $activityExtension));
        $statement->setActor($this->buildAgent());
        if (isset($contextId) && isset($contextName) && isset($contextActivityType)) {
            $statement->setContext($this->buildContext($contextId, $contextName, $contextActivityType));
        }
        $statement->setId(Str::orderedUuid()->toString());
        return $statement;
    }

    /**
     * Builds a xAPI activity
     *
     * @param \App\Enums\XapiActivityType $activityType
     * @param \App\Enums\XapiActivityDescription $activityDescription
     * @param array $activityName
     * @param String|null $objectId
     * @param array|null $activityExtension
     * @return Activity
     */
    private function buildActivity(string $objectId,
                                   XapiActivityType $activityType,
                                   array $activityName,
                                   ?XapiActivityDescription $activityDescription,
                                   ?array $activityExtension = null): Activity
    {
        $object = new Activity();
        $object->setId($objectId);

        $actDef = new ActivityDefinition();
        $actDef->setType(XapiActivityType::getActivityType($activityType->value));
        $actDef->setName($activityName);
        if (isset($activityDescription)) {
            $actDef->setDescription(XapiActivityDescription::getDesc($activityDescription->value));
        }
        if (isset($activityExtension)) {
            $actDef->setExtensions($activityExtension);
        }

        $object->setDefinition($actDef);
        return $object;
    }

    /**
     * Builds a xAPI verb
     *
     * @param $verb
     * @return Verb
     */
    private function buildVerb($verb): Verb
    {
        $verbObj = new Verb();
        $verbObj->setDisplay(XapiVerb::getDisplayName($verb->value));
        $verbObj->setId($verb->description);
        return $verbObj;
    }

    /**
     * Builds a xAPI agent
     *
     * @return Agent
     */
    private function buildAgent(): Agent
    {
        $actor = new Agent();
        $actor->setName(Auth::user()->name);
        $actor->setMbox(Auth::user()->email);
        return $actor;
    }

    /**
     * @param string $contextId
     * @param array $contextName
     * @param XapiActivityType $contextActivityType
     * @return Context
     */
    private function buildContext(string $contextId,
                                  array $contextName,
                                  XapiActivityType $contextActivityType): Context
    {
        $context = new Context();
        $contextAc = new ContextActivities();

        $contextAc->setParent($this->buildActivity($contextId, $contextActivityType, $contextName, null, null));
        $context->setContextActivities($contextAc);
        return $context;
    }

    private function addUserRoleToExtension($activityExtension): array
    {
        $userRole = Auth::user()->getRoleNames()->first();
        $userRoleUrl = url('/userRole');

        if($activityExtension == null) {
            $activityExtension = [];
        }

        $activityExtension[$userRoleUrl] = $userRole;

        return $activityExtension;
    }

    private function addProjectDetailsToExtension($activityExtension, $projectId)
    {
        if (!$projectId || $projectId === null) {
            return $activityExtension;
        }

        $project = $this->projectRepository->get($projectId);
        if (!$project) {
            Log::info('could not find project for xapi statement by id:'. $projectId);
            return;
        }

        $projectTypeUrl = url('/projectType');
        $projectIdUrl = url('/projectId');

        if ($activityExtension == null) {
            $activityExtension = [];
        }

        $activityExtension[$projectTypeUrl] = $project->type;
        $activityExtension[$projectIdUrl] = $project->id;

        return $activityExtension;
    }
}
