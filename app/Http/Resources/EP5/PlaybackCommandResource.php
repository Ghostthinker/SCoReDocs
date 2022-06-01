<?php

namespace App\Http\Resources\EP5;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaybackCommandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'duration' => $this->duration,
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'title' => $this->title ?? '',
            'type' => $this->type,
            'date_formatted' => $this->created_at->format(config('app')['date_format']),
            'sequence_id' => $this->sequence_id,
        ];

        $additional_fields = $this->additional_fields;
        $data = array_merge($additional_fields, $data);
        $user = $this->user;

        if ($user) {
            $data['userdata'] = [
                'name' => $user->name,
                'uid' => $user->id,
                'picture' => '/assets/images/default_user.png',
            ];
        }
        return $data;
    }
}
