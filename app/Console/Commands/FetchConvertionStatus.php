<?php

namespace App\Console\Commands;

use App\Enums\MediaStatus;
use App\Services\UploadService;
use Illuminate\Console\Command;

class FetchConvertionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:update-convert-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This directive updates the convert status of pending video uploads';

    private $service;

    /**
     * Create a new command instance.
     *
     * @param  UploadService  $service
     */
    public function __construct(UploadService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updatedPendingMedia = $this->service->updateMediaStatusByCurrentStatus(MediaStatus::PENDING);
        $updatedCreatedMedia = $this->service->updateMediaStatusByCurrentStatus(MediaStatus::CREATED);
        $updatedFailedMedia = $this->service->updateMediaStatusByCurrentStatus(MediaStatus::FAILED_CONVERT);
        $total = array_merge([$updatedPendingMedia, $updatedCreatedMedia, $updatedFailedMedia]);
        $this->info(count($total).' were updated.');
    }
}
