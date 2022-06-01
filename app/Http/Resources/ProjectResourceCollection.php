<?php

namespace App\Http\Resources;

use App\Rules\PermissionSet;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProjectResourceCollection extends ResourceCollection
{

    private $projects;

    public function __construct($projects)
    {
        $this->projects = $projects;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $canDuplicateProject = Auth::getUser()->can(PermissionSet::CAN_DUPLICATE_PROJECT);
        $canChangeProjectType = Auth::getUser()->can(PermissionSet::CAN_CHANGE_PROJECT_TYPE);

        return [
            'projects' => ProjectResource::collection($this->projects),
            'meta' => [
                'canDuplicateProject' => $canDuplicateProject,
                'canChangeProjectType' => $canChangeProjectType
            ]
        ];
    }
}
