<?php

namespace App\Services;

use App\Models\DataExport;
use App\Repositories\ActivityRepository;
use App\Repositories\AuditRepository;
use App\Repositories\DataExportRepositoryInterface;
use App\Repositories\EP5\AnnotationRepository;
use App\Repositories\EP5\PlaybackCommandRepository;
use App\Repositories\EP5\VideoSequenceRepository;
use App\Repositories\LinkRepository;
use App\Repositories\MediaRepository;
use App\Repositories\MessageRepository;
use App\Repositories\NewsRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SectionRepository;
use App\Services\Xapi\XapiService;
use DateTime;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception;
use TinCan\Statement;

class DataExportService
{
    const STATEMENT_LIMIT = 10000;
    const ITEMS_TO_KEEP = 10;

    private $dataExportRepository;
    private $xapiService;
    private $dataMap = [
        'sectionId' => SectionRepository::class,
        'projectId' => ProjectRepository::class,
        'videoId' => MediaRepository::class,
        'linkId' => LinkRepository::class,
        'playbackId' => PlaybackCommandRepository::class,
        'commentId' => AnnotationRepository::class,
        'revisionId' => AuditRepository::class,
        'revisionOne' => AuditRepository::class,
        'revisionTwo' => AuditRepository::class,
        'mediaSequenceId' => VideoSequenceRepository::class,
        'mediaId' => MediaRepository::class,
        'mentioningTargetUser' => ProfileRepository::class,
        'mentioningSourceUser' => ProfileRepository::class,
        'messageId' => MessageRepository::class,
        'activityId' => ActivityRepository::class,
        'newsId' => NewsRepository::class
    ];

    public function __construct(DataExportRepositoryInterface $repository, XapiService $xapiService)
    {
        $this->dataExportRepository = $repository;
        $this->xapiService = $xapiService;
    }

    /**
     * @return DataExport|void
     * @throws Exception
     */
    public function generateNewDataExport(): DataExport
    {
        $lastDataExport = $this->dataExportRepository->getLatest()->first();
        $dataExport = $this->dataExportRepository->create([]);

        $lastTimestamp = $lastDataExport ? $lastDataExport->created_at->format(DateTime::ISO8601) : null;
        $response = $this->xapiService->getStatements($lastTimestamp,
            $dataExport->created_at->format(DateTime::ISO8601), self::STATEMENT_LIMIT);
        if (!$response || $response->success === false) {
            throw new Exception(json_encode($response));
        }
        $statements = $response->content->getStatements();
        $json = $this->parseStatements($statements);

        while (count($statements) === self::STATEMENT_LIMIT) {
            $lastTimestamp = end($statements)->getTimestamp();
            $response = $this->xapiService->getStatements($lastTimestamp,
                $dataExport->created_at->format(DateTime::ISO8601), self::STATEMENT_LIMIT);
            $statements = $response->content->getStatements();
            $json = array_merge($json, $this->parseStatements($statements));
        }

        $this->storeData($dataExport, $lastDataExport, $json);
        return $dataExport;
    }

    public function removeOldDataExports()
    {
        $dataExportsToKeep = $this->dataExportRepository->getLatest()
            ->take(self::ITEMS_TO_KEEP)
            ->pluck('id');
        $dataExportsToRemove = $this->dataExportRepository->whereNotIn('id', $dataExportsToKeep->toArray());
        foreach ($dataExportsToRemove as $dataExport) {
            $this->dataExportRepository->delete($dataExport->id);
            Storage::disk('data_export')->delete($dataExport->filename);
        }
    }

    /**
     * @param $statements Statement[]
     * @return array
     */
    private function parseStatements(array $statements)
    {
        $output = [];
        foreach ($statements as $statement) {
            // Statement
            $statementAsJson = $statement->asVersion('1.0.0');
            $statementWithAdditionalData = [
                'Statement' => $statementAsJson
            ];

            // Extensions
            $extensions = $statement->getTarget()->getDefinition()->getExtensions()->asVersion();
            if ($extensions) {
                foreach ($extensions as $key => $value) {
                    $searchParts = explode('/', $key);
                    $searchValue = end($searchParts);
                    $object = $this->getDataObject($searchValue, $value);
                    if ($object) {
                        $statementWithAdditionalData = array_merge($statementWithAdditionalData, $object);
                    }
                }
            }
            // Context
            if ($statement->getContext()) {
                $parent = $statement->getContext()->getContextActivities()->getParent();
                $searchValue = $parent[0]->getId();
                $searchParts = explode('/', $searchValue);
                $searchValue = $searchParts[count($searchParts) - 2];
                $object = $this->getDataObject($searchValue, end($searchParts));
                if ($object) {
                    $statementWithAdditionalData = array_merge($statementWithAdditionalData, $object);
                }
            }
            $output = array_merge($output, [$statement->getId() => $statementWithAdditionalData]);
        }
        return $output;
    }

    /**
     * @param $searchValue
     * @param $value
     * @return null
     */
    private function getDataObject($searchValue, $value)
    {
        try {
            $repoType = $this->dataMap[$searchValue];
        } catch (\Exception $exception) {
            return null;
        }
        if (!empty($repoType)) {
            $repo = new $repoType();
            $elo = $repo->get($value);
            if ($elo) {
                $elo->setAppends([]);
                return [class_basename($elo) => $elo->toArray()];
            }
        }
    }

    /**
     * @param $dataExport DataExport
     * @param  DataExport|null  $lastDataExport
     * @param $data
     */
    private function storeData(DataExport $dataExport, ?DataExport $lastDataExport, $data)
    {
        $dataExport->filename = 'data_export_'.$dataExport->id.'.json';
        $dataExport->statement_count = $lastDataExport ? ($lastDataExport->statement_count + count($data)) : count($data);

        // Last DataExport is copied and new data is append to copy
        if ($lastDataExport) {
            Storage::disk('data_export')->copy($lastDataExport->filename, $dataExport->filename);
            Storage::disk('data_export')->append($dataExport->filename, json_encode($data));
        } else {
            Storage::disk('data_export')->put($dataExport->filename, json_encode($data));
        }

        $dataExport->filesize = Storage::disk('data_export')->size($dataExport->filename);
        $dataExport->path = Storage::disk('data_export')->path($dataExport->filename);
        $dataExport->save();
    }
}
