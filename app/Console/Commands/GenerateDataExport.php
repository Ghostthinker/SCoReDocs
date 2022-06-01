<?php

namespace App\Console\Commands;

use App\Services\DataExportService;
use Exception;
use Illuminate\Console\Command;

class GenerateDataExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:generate-data-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data export.';
    private $dataExportService;

    /**
     * Create a new command instance.
     *
     * @param  DataExportService  $dataExportService
     */
    public function __construct(DataExportService $dataExportService)
    {
        $this->dataExportService = $dataExportService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $dataExport = $this->dataExportService->generateNewDataExport();
            $this->dataExportService->removeOldDataExports();
            $this->info('Data export '.$dataExport->filename.' was generated with '.$dataExport->statement_count.' statements');
        } catch (Exception $exception) {
            $this->info('Could not generate Data Export: ' . $exception->getMessage());
        }
    }
}
