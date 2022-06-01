<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCountEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $projectId;
    private $sectionId;
    private $messageId;

    /**
     * Create a new event instance.
     *
     * @param $projectId
     * @param $messageId
     * @param  null  $sectionId
     */
    public function __construct($projectId,  $messageId, $sectionId = null)
    {
        $this->projectId = $projectId;
        $this->sectionId = $sectionId;
        $this->messageId = $messageId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newMessageCount';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->sectionId !== null) {
            return new Channel('messagecount.' . $this->projectId . '-section.' . $this->sectionId);
        }
        return new Channel('messagecount.' . $this->projectId);
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
            'messageId' => $this->messageId
        ];
    }
}
