<?php

namespace App\Http\Resources;

use App\Enums\ProjectStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->assessmentDocOwner->name,
            'matrNr' => $this->assessmentDocOwner->profile->matriculation_number,
            'email' => $this->assessmentDocOwner->email,
            'assessmentdoclink' => url("/project/{$this->id}"),
            'status' => $this->status,
            'statusText' => ProjectStatus::getDescription($this->status),
        ];
    }
}
