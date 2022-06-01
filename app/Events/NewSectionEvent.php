<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSectionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $sectionId;
    private $projectId;
    private $indexArray;

    /**
     * Create a new event instance.
     *
     * @param $sectionId
     * @param $projectId
     * @param $indexArray
     */
    public function __construct($sectionId, $projectId, $indexArray)
    {
        $this->sectionId = $sectionId;
        $this->projectId = $projectId;
        $this->indexArray = $indexArray;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newSection';
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
            'indexArray' => $this->indexArray,
            'projectId' => $this->projectId,
            'sectionId' => $this->sectionId,
        ];
    }
}
