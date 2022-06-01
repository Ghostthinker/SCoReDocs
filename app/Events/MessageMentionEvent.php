<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageMentionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $projectId;
    private $sectionId;
    private $mentionedUserId;
    private $title;
    private $id;

    /**
     * Create a new event instance.
     *
     * @param  null  $sectionId
     * @param $messageId
     * @param $projectId
     * @param $title
     *
     */
    public function __construct($id, $messageId, $projectId, $mentionedUserId, $title, $sectionId = null)
    {
        $this->messageId = $messageId;
        $this->projectId = $projectId;
        $this->mentionedUserId = $mentionedUserId;
        $this->sectionId = $sectionId;
        $this->title = $title;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newMessageMention';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messageMention.' . $this->projectId . '-user.' . $this->mentionedUserId);
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'messageId' => $this->messageId,
            'projectId' => $this->projectId,
            'sectionId' => $this->sectionId,
            'title' => $this->title,
            'id' => $this->id
        ];
    }
}
