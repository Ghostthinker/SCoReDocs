<?php

namespace App\Console\Commands;

use App\Services\TimeoutService;
use Illuminate\Console\Command;

class RunUnlockTimeouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:run-unlock-timeouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock all timed out sections.';
    private $timeoutService;

    /**
     * Create a new command instance.
     *
     * @param  TimeoutService  $timeoutService
     */
    public function __construct(TimeoutService $timeoutService)
    {
        parent::__construct();
        $this->timeoutService = $timeoutService;
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $sections = $this->timeoutService->unlockTimedOutSections();
        $this->info(count($sections).' were unlocked.');
    }
}
