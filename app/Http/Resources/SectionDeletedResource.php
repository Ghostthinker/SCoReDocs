<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionDeletedResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user_id' => $this->audits->first()->user_id,
            'name' => $this->audits->first()->user->name,
            'change_log' => $this->audits->first()->change_log,
            'deleted_at' => $this->deleted_at->setTimezone('Europe/Berlin')->format('d.m.Y - H:i:s'),
        ];
    }
}
