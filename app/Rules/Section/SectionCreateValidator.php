<?php

namespace App\Rules\Section;

use App\Enums\ProjectType;
use App\Enums\SectionStatus;
use App\Rules\PermissionSet;
use Illuminate\Support\Facades\Auth;

class SectionCreateValidator extends SectionValidator
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute The attribute name
     * @param int $value The new content
     *
     * @return bool Indicates if content change is valid
     */
    public function passes($attribute, $value)
    {
        if (!($attribute === 'topSectionId')) {
            return true;
        }

        $section = $this->getSection();
        $project = $this->getProject();

        if ($project !== null && $project->type === ProjectType::ASSESSMENT_DOC) {
            if (!self::entitledToAddSectionToAssessment() || !self::entitledToAddSectionToLockedSection($section)) {
                $this->setMessage('Fehlende Berechtigung um an dieser Position einen Abschnitt zu erstellen');
                return false;
            }
        }

        if (!self::entitledToAddSectionToLockedSection($section)) {
            $this->setMessage('Fehlende Berechtigung um an dieser Position einen Abschnitt zu erstellen');
            return false;
        }

        return true;
    }

    private static function entitledToAddSectionToLockedSection($section)
    {
        if ($section->status === SectionStatus::EDIT_NOT_POSSIBLE ||
            $section->status === SectionStatus::IN_REVIEW ||
            $section->status === SectionStatus::COMPLETED) {
            if (!Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_LOCKED_SECTION)) {
                return false;
            }
        }
        return true;
    }

    private static function entitledToAddSectionToAssessment()
    {
        if (Auth::getUser()->can(PermissionSet::CAN_ADD_SECTION_TO_ASSESSMENT)) {
            return true;
        }
        return false;
    }
}
