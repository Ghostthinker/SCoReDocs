<?php

namespace App\Http\Resources\EP5;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoSequencePlayListResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $author = $this->author;
        return [
            'id' => $this->id,
            'video_nid' => $this->video_nid,
            'title' => $this->title,
            'video_title' => $this->media->caption,
            'duration' => $this->duration,
            'timestamp' => $this->timestamp,
            'thumbnail' => $this->preview_thumb,
            'updated_at' => $this->last_modified_timestamp,
            'type' => 'Sequence',
            'userdata' => [
                'name' => $author ? $author['name'] : '',
                'uid' => $author ? $author['id'] : '',
                'picture' => $author && $author['image'] ? $author['image'] : '/assets/images/default_user.png',
            ]
        ];
    }
}
