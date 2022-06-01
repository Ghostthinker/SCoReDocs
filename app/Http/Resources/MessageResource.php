<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    protected $actualUser;

    public function actualUser($value)
    {
        $this->actualUser = $value;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $userId = $this->user_id;
        $data = $this->data;
        $data['meta'] = $this->created_at->setTimezone('Europe/Berlin')->format(config('app')['date_format']);
        return [
            'id' => $this->id,
            'type' => $this->type,
            'author' => $userId == $this->actualUser ? 'me' : $userId,
            'data' => $data,
            'sectionTitle' => $this->section ? $this->section->title : '',
            'sectionId' => $this->section ? $this->section->id : null,
            'parent' => $this->parent,
            'parentAuthor' => $this->parent ? $this->parent->user->name : null,
        ];
    }
}
