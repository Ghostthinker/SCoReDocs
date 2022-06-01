<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'university' => $this->university,
            'course' => $this->course,
            'matriculationNumber' => $this->matriculation_number,
            'knowledge' => $this->knowledge,
            'personalResources' => $this->personal_resources,
            'aboutMe' => $this->about_me,
            'avatar' => $this->avatar,
        ];
    }
}
