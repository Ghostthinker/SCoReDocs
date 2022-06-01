<?php

namespace App\Http\Resources;

use App\Rules\PermissionSet;
use Auth;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SectionResourceCollection extends ResourceCollection
{
    protected $actualUser;

    public function actualUser($value)
    {
        $this->actualUser = $value;
        return $this;
    }

    public function toArray($request)
    {

        $canAddSectionToAssessment = Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT);
        $canAddSectionToLockedSection = Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_LOCKED_SECTION);
        return $this->collection->map(function (SectionResource $resource) use ($request, $canAddSectionToAssessment, $canAddSectionToLockedSection) {
            return $resource->actualUser($this->actualUser)->additional(['canAddSectionToAssessment' => $canAddSectionToAssessment, 'canAddSectionToLockedSection' => $canAddSectionToLockedSection])->toArray($request);
        })->all();
    }
}
