<?php

namespace App\Console\Commands;


use App\Services\DailyDigestService;
use Carbon\Carbon;
use Illuminate\Console\Command;


class DailyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'score:daily-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Daily Digest.';
    private $dailyDigestService;

    /**
     * Create a new command instance.
     *
     * @param  DailyDigestService  $dailyDigestService
     */
    public function __construct(DailyDigestService $dailyDigestService)
    {
        $this->dailyDigestService = $dailyDigestService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $this->dailyDigestService->createDigest($startDate, $endDate);
    }


}
