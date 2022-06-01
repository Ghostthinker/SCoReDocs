<?php

namespace App\Events;

use App\Http\Resources\NewsResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNewsEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $news;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(NewsResource $news)
    {
        $this->news = $news;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newNews';
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
            'news' => $this->news
        ];
    }
}
