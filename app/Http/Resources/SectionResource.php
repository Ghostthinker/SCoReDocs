<?php

namespace App\Http\Resources;

use App\Enums\ProjectType;
use App\Enums\SectionStatus;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionContentValidator;
use App\Rules\Section\SectionDeleteValidator;
use App\Rules\Section\SectionHeadingTypeValidator;
use App\Rules\Section\SectionStatusValidator;
use App\User;
use Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    protected $actualUser;

    public function actualUser($value)
    {
        $this->actualUser = $value;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lockingUser = $this->locking_user;
        $status = $this->status ? $this->status : 0;
        $lockedAt = $this->locked_at ? $this->locked_at->format(config('app')['date_format']) : null;
        $addSectionPossible = true;


        $project = $this->project;
        if ($project->type === ProjectType::ASSESSMENT_DOC) {
           if ($this->additional && !$this->additional['canAddSectionToAssessment']) {
               $addSectionPossible = false;
           } elseif(!Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT)){
               $addSectionPossible = false;
           }
        }

        if ($this->status === SectionStatus::EDIT_NOT_POSSIBLE ||
            $this->status === SectionStatus::IN_REVIEW ||
            $this->status === SectionStatus::COMPLETED) {
            if ($this->additional && !$this->additional['canAddSectionToLockedSection']) {
                $addSectionPossible = false;
            } elseif(!Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_LOCKED_SECTION)){
                $addSectionPossible = false;
            }
        }
        $heading = (int) $this->heading;
        $sectionDelVal = new SectionDeleteValidator($this->resource);

        $isCollapse = $this->user_collapse->count();


        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content ? $this->content : '',
            'heading' => $heading,
            'index' => $this->index,
            'locked' => $this->locked ? true : false,
            'locked_at' => $lockedAt,
            'locking_user' => $lockingUser,
            'locked_by_me' => $lockingUser == $this->actualUser,
            'project_id' => $this->project_id,
            'status' => $status,
            'statusText' => SectionStatus::getDescription($status),
            'addSectionPossible' => $addSectionPossible,
            'entitled_to_change_content' => SectionContentValidator::entitledToChangeContent($this),
            'entitled_to_change_heading_type' => SectionHeadingTypeValidator::entitledToChangeHeading($this),
            'entitled_to_change_status' => SectionStatusValidator::userIsEntitledToChangeStatus($this->status),
            'user_can_delete' => $sectionDelVal->passes('delete', '0'),
            'isCollapse' => $isCollapse ? 1 : 0
        ];
        if ($lockingUser !== null) {
            $data['username'] = User::find($lockingUser)->name;
        }

        return $data;
    }
}
