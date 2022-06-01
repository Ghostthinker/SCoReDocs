<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $user = User::find($this->user_id);
        $data['user_name'] = $user ? $user->name : 'SCoRe';
        $data['created_at'] = $this->created_at->setTimezone('Europe/Berlin')->format('d.m.Y - H:i:s');
        $data['updated_at'] = $this->updated_at->setTimezone('Europe/Berlin')->format('d.m.Y - H:i:s');
        $data['state'] = json_decode($this->state);
        return $data;
    }
}
