<?php

namespace App\Http\Resources;

use App\Rules\PermissionSet;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user_can_download_media = false;
        if (Auth::getUser()->can(PermissionSet::CAN_DOWNLOAD_MEDIA)) {
            $user_can_download_media = true;
        }
        $is_user_watching_project = $this->isAuthUserWatching();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'type' => $this->type,
            'description' => $this->description,
            'assessment_doc_owner_id' => $this->assessment_doc_owner_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'user_can_download_media' => $user_can_download_media,
            'is_user_watching_project' => $is_user_watching_project,
            'basic_course' => $this->basic_course
        ];
    }
}
