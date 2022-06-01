<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataUniversitiesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'university' => $this->university,
        ];
    }
}
