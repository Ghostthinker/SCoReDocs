<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = Auth::user();
        $data = parent::toArray($request);
        $data['created_at'] = $this->created_at->setTimezone('Europe/Berlin')->format('d.m.Y');
        $data['updated_at'] = $this->updated_at->setTimezone('Europe/Berlin')->format('d.m.Y');
        $data['updated_at_timestamp'] = $this->updated_at;
        $data['created_at_timestamp'] = $this->created_at;
        $data['read'] = (bool) $this->usersRead()->where('user_id', $user->id)->first();
        return $data;
    }
}
