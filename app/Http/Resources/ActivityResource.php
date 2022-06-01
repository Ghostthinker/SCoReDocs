<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
          'userId' => $this->user_id,
          'type' => $this->type,
          'message' => $this->message,
          'sectionId' => $this->section_id,
          'projectId' => $this->project_id,
          'projectTitle' => $this->project->title,
          'sectionTitle' => $this->section ? $this->section->title : '',
          'isSectionDeleted' => $this->section ? !!$this->section->deleted_at : null,
          'read' => count($this->user_read) > 0,
          'targetUserIds' => !empty($this->additional['targetUserIds']) ? $this->additional['targetUserIds'] : [],
        ];
    }
}
