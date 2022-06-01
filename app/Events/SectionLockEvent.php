<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SectionLockEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $sectionId;
    private $projectId;

    /**
     * Create a new event instance.
     *
     * @param $sectionId
     * @param $projectId
     */
    public function __construct($sectionId, $projectId)
    {
        $this->sectionId = $sectionId;
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'lockSection';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('section.' . $this->projectId);
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
