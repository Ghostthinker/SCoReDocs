<?php

namespace App\Events;

use App\Http\Resources\ActivityResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewActivityEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $activity;

    public function __construct(ActivityResource $activity)
    {
        $this->activity = $activity;
    }

    public function broadcastAs() {
        return 'newActivity';
    }

    public function broadcastOn()
    {
        return new Channel('activity');
    }

    public function broadcastWith() {
        return [
            'activity' => $this->activity
        ];
    }

    public function getActivity() {
        return $this->activity;
    }
}
