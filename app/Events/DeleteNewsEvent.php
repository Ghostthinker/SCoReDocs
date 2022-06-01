<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteNewsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $newsId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($newsId)
    {
        $this->newsId = $newsId;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'deleteNews';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('news');
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'news_id' => $this->newsId
        ];
    }
}
