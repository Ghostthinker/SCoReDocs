<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSectionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $section;

    /**
     * Create a new event instance.
     *
     * @param $section
     */
    public function __construct($section)
    {
        $this->section = $section;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'updateSection';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('section.' . $this->section->project_id);
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'section' => $this->section,
        ];
    }
}
