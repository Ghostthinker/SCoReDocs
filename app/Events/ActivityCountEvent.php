<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityCountEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $projectId;
    private $sectionId;

    /**
     * Create a new event instance.
     *
     * @param $projectId
     * @param $sectionId
     */
    public function __construct($projectId, $sectionId)
    {
        $this->projectId = $projectId;
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newActivityCount';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('activitycount.' . $this->projectId);
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'projectId' => $this->projectId,
            'sectionId' => $this->sectionId,
        ];
    }
}
