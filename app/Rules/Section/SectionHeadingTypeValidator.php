<?php

namespace App\Rules\Section;

use App\Enums\SectionStatus;
use App\Rules\PermissionSet;
use Illuminate\Support\Facades\Auth;

class SectionHeadingTypeValidator extends SectionValidator
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute  The attribute name
     * @param  int  $value  The new heading type
     *
     * @return bool Indicates if heading type change is valid
     */
    public function passes($attribute, $value)
    {
        if (!$attribute === 'heading') {
            return true;
        }
        if (!$this->hasChanges($attribute, $value)) {
            return true;
        }
        if (!$this->entitledToChangeHeadingByHeading($this->getSection()->heading)) {
            $this->setMessage('Fehlende Berechtigung um den Typ der Überschrift eines Abschnittes mit Überschrift 1 oder 2 zu ändern.');
            return false;
        }
        if (!$this->entitledToChangeHeadingByStatus($this->getSection()->status)) {
            $this->setMessage('Fehlende Berechtigung um den Typ der Überschrift eines Abschnittes im Status "'. SectionStatus::getDescription($this->getSection()->status).'" zu ändern.');
            return false;
        }
        if (!$this->entitledToSetHeading($value)) {
            $this->setMessage('Fehlende Berechtigung um den Typ der Überschrift eines Abschnittes mit Überschrift 1 oder 2 zu ändern.');
            return false;
        }
        return true;
    }

    /**
     * Checks if user is entitled to change heading
     *
     * @param Section $section The heading
     *
     * @return bool Indicator if user is entitled
     */
    public static function entitledToChangeHeading($section)
    {
        return self::entitledToChangeHeadingByHeading($section->heading)
            && self::entitledToChangeHeadingByStatus($section->status);
    }

    /**
     * Checks if user is entitled to change heading type
     *
     * @param int $newHeading The new heading that should be set
     *
     * @return bool Indicator if user is entitled
     */
    public static function entitledToSetHeading($newHeading)
    {
        if ($newHeading > 2) {
            return true;
        }
        if ($newHeading == 1) {
            return Auth::getUser()->can(PermissionSet::SET_HEADING_1_TYPE);
        }
        if ($newHeading == 2) {
            return Auth::getUser()->can(PermissionSet::SET_HEADING_2_TYPE);
        }
        return false;
    }

    /**
     * Checks if user is entitled to change the heading type depending on the status
     *
     * @param  int  $status  The status to change
     *
     * @return bool Indicator if user is entitled
     */
    public static function entitledToChangeHeadingByStatus($status)
    {
        if ($status == SectionStatus::EDIT_NOT_POSSIBLE) {
            return Auth::getUser()->can(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE);
        }
        if ($status == SectionStatus::IN_REVIEW) {
            return Auth::getUser()->can(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW);
        }
        if ($status == SectionStatus::COMPLETED) {
            return Auth::getUser()->can(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED);
        }
        return true;
    }

    /**
     * Checks if user is entitled to change heading type depending on heading
     *
     * @param int $heading The heading
     *
     * @return bool Indicator if user is entitled
     */
    private static function entitledToChangeHeadingByHeading($heading)
    {
        if ($heading > 2) {
            return true;
        }
        if ($heading == 1) {
            return Auth::getUser()->can(PermissionSet::CHANGE_HEADING_1_TYPE);
        }
        if ($heading == 2) {
            return Auth::getUser()->can(PermissionSet::CHANGE_HEADING_2_TYPE);
        }
        return false;
    }
}
