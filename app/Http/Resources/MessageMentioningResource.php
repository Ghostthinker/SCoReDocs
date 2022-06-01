<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageMentioningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->message->section ? $this->message->section->title : $this->project->title,
            'sectionId' => optional($this->message->section)->id,
            'projectId' => $this->project->id,
            'messageId' => $this->message_id
        ];
    }
}
