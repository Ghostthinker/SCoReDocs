<?php

namespace App\Jobs;

use App\Facades\Xapi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessXapi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $statement;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Xapi::sendStatement($this->statement);
    }
}
