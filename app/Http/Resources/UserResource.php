<?php

namespace App\Http\Resources;

use App\Rules\PermissionSet;
use Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'assessment_doc_id' => $this->assessment_doc_id,
            'meta' => [
                'hasSeenIntroVideo' => $this->hasSeenIntro,
                'canAccessUserAdministration' => Auth::user()->hasRole('Admin'),
                'canAccessAssessmentOverview' => Auth::user()->can(PermissionSet::CAN_VIEW_ASSESSMENT_DOCS_OVERVIEW),
                'canAccessTemplate' => Auth::user()->can(PermissionSet::EDIT_TEMPLATES),
                'canAccessDataExport' => Auth::user()->can(PermissionSet::CAN_EXPORT_DATA),
                'canAccessAssessmentDoc' => Auth::user()->assessment_doc_id !== null,
                'canAccessDownloadAgreementDataProcessing' => Auth::user()->can(PermissionSet::CAN_DOWNLOAD_AGREEMENTS_DATA_PROCESSING),
                'leftMenuCollapsed' => $this->left_menu_collapsed,
                'rightMenuCollapsed' => $this->right_menu_collapsed
            ]
        ];
    }
}
