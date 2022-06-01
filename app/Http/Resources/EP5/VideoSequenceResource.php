<?php

namespace App\Http\Resources\EP5;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoSequenceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'video_nid' => $this->video_nid,
            'title' => $this->title,
            'description' => $this->description,
            'duration' => $this->duration,
            'timestamp' => $this->timestamp,
            'camera_look_at' => $this->camera_look_at,
            'camera_locked' => $this->camera_locked,
            'camera_path' => $this->camera_path
        ];
    }
}
