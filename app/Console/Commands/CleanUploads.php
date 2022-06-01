<?php

namespace App\Console\Commands;

use App\Enums\MediaStatus;
use App\Repositories\MediaRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:clean-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up uploads.';
    private $mediaRepository;

    /**
     * Create a new command instance.
     *
     * @param  MediaRepository  $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        parent::__construct();
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->cleanupUploads();
        $this->info($count.' uploads were cleaned.');
    }

    private function cleanupUploads(): int
    {
        $count = 0;
        $mediaFiles = $this->mediaRepository->getMediaByStatus([MediaStatus::CONVERTED, MediaStatus::FAILED_CONVERT]);

        foreach ($mediaFiles as $media) {
            $count++;
            Storage::delete('uploads/'.$media->id);
        }
        return $count;
    }
}
