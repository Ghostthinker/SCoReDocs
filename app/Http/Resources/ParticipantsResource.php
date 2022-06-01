<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => optional($this->profile)->avatar,
            'deleted' => $this->trashed(),
            'showUserInParticipantList' => (bool) $this->showUserInParticipantList
        ];
    }
}
