<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $projectId;
    private $sectionId;

    /**
     * Create a new event instance.
     *
     * @param MessageResource $message
     * @param $projectId
     * @param $sectionId
     */
    public function __construct(MessageResource $message, $projectId, $sectionId = null)
    {
        $this->message = $message;
        $this->projectId = $projectId;
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newMessage';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->sectionId !== null) {
            return new PresenceChannel('message.' . $this->projectId . '-section.' . $this->sectionId);
        }
        return new PresenceChannel('message.' . $this->projectId);
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'projectId' => $this->projectId,
            'sectionId' => $this->sectionId,
        ];
    }

    /**
     * @return MessageResource
     */
    public function getMessage(): MessageResource
    {
        return $this->message;
    }
}
