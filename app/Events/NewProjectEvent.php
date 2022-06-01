<?php

namespace App\Events;

use App\Http\Resources\ProjectResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewProjectEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;

    /**
     * Create a new event instance.
     *
     * @param ProjectResource $project
     */
    public function __construct(ProjectResource $project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'newProject';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('project');
    }

    /**
     * Data which is also to be broadcasted
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'project' => $this->project
        ];
    }
}
