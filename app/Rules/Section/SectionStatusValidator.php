<?php

namespace App\Rules\Section;

use App\Enums\SectionStatus;
use App\Rules\PermissionSet;
use Illuminate\Support\Facades\Auth;

class SectionStatusValidator extends SectionValidator
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute  The attribute name
     * @param  int  $value  The new status
     *
     * @return bool Indicates if status change is valid
     */
    public function passes($attribute, $value)
    {
        if ($attribute !== 'status') {
            return true;
        }
        if (!$this->hasChanges($attribute, $value)) {
            return true;
        }
        if (!$this->isExistingStatus($value)) {
            $this->setMessage('Status ist unbekannt.');
            return false;
        }
        if (!$this->userIsEntitledToSetStatus($value)) {
            $this->setMessage('Der Benutzer ist nicht berechtigt den Status auf '.
                SectionStatus::getDescription($value).
                ' zu setzen!');
            return false;
        }
        if (!$this->userIsEntitledToChangeStatus($this->getSection()[$attribute])) {
            $this->setMessage('Der Benutzer ist nicht berechtigt den Status '.
                SectionStatus::getDescription($this->getSection()[$attribute]).
                ' zu ändern!');
            return false;
        }
        if ($value === SectionStatus::SUBMITTED) {
            if (!$this->childrenAreSubmitted($this->getSection())) {
                $this->setMessage('Abschnitt kann erst eingereicht werden, wenn alle Unterabschnitte eingereicht sind.');
                return false;
            }
        }
        if (!Auth::getUser()->can(PermissionSet::BREAK_SECTION_WORKFLOW)) {
            if (!$this->isAValidWorkflow($this->getSection()->status, $value)) {
                $this->setMessage('Diese Änderung des Status ist nicht valide.');
                return false;
            }
        }
        return true;
    }

    /**
     * Check if status is a valid number in Enum
     *
     * @param  int  $value  The new status
     *
     * @return bool Indicator if status is
     */
    public static function isExistingStatus($value)
    {
        return SectionStatus::hasValue($value, false);
    }

    /**
     * Checks if user is entitled to set status
     *
     * @param  int  $value  The status
     *
     * @return bool Indicator if user is entitled to set status
     */
    public static function userIsEntitledToSetStatus($value)
    {
        switch ($value) {
            case SectionStatus::EDIT_NOT_POSSIBLE:
                return Auth::getUser()->can(PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE);
            case SectionStatus::IN_PROGRESS:
                return Auth::getUser()->can(PermissionSet::SET_STATUS_IN_PROGRESS);
            case SectionStatus::SUBMITTED:
                return Auth::getUser()->can(PermissionSet::SET_STATUS_SUBMITTED);
            case SectionStatus::IN_REVIEW:
                return Auth::getUser()->can(PermissionSet::SET_STATUS_IN_REVIEW);
            case SectionStatus::COMPLETED:
                return Auth::getUser()->can(PermissionSet::SET_STATUS_COMPLETED);
            default:
                return false;
        }
    }

    /**
     * Checks if user is entitled to change status
     *
     * @param  int  $value  The status
     *
     * @return bool Indicator if user is entitled to change status
     */
    public static function userIsEntitledToChangeStatus($value)
    {
        switch ($value) {
            case SectionStatus::EDIT_NOT_POSSIBLE:
                return Auth::getUser()->can(PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE);
            case SectionStatus::IN_PROGRESS:
                return Auth::getUser()->can(PermissionSet::CHANGE_STATUS_IN_PROGRESS);
            case SectionStatus::SUBMITTED:
                return Auth::getUser()->can(PermissionSet::CHANGE_STATUS_SUBMITTED);
            case SectionStatus::IN_REVIEW:
                return Auth::getUser()->can(PermissionSet::CHANGE_STATUS_IN_REVIEW);
            case SectionStatus::COMPLETED:
                return Auth::getUser()->can(PermissionSet::CHANGE_STATUS_COMPLETED);
            default:
                return false;
        }
    }

    /**
     * Checks if all child sections are in submitted state when submitting the parent
     *
     * @param $section
     *
     * @return bool
     */
    public static function childrenAreSubmitted($section)
    {
        foreach ($section->getChildren() as $section) {
            if ($section->status !== SectionStatus::SUBMITTED) {
                return false;
            }
        }
        return true;
    }

    /**
     * Method indicates if the workflow is valid
     *
     * @param  int  $oldStatus  The current status of the section
     * @param  int  $newStatus  The new status of the section
     *
     * @return bool Indicates the success of the check
     */
    public static function isAValidWorkflow($oldStatus, $newStatus)
    {
        // no state changed
        if ($oldStatus === $newStatus) {
            return true;
        }
        // You can always go between Submitted and Progress
        if (($oldStatus == SectionStatus::IN_PROGRESS && $newStatus == SectionStatus::SUBMITTED) ||
            ($oldStatus == SectionStatus::SUBMITTED && $newStatus == SectionStatus::IN_PROGRESS)) {
            return true;
        }
        // You can jump from inReview to InProgress or Completed
        if ($oldStatus == SectionStatus::IN_REVIEW && ($newStatus == SectionStatus::IN_PROGRESS ||
                $newStatus == SectionStatus::COMPLETED)) {
            return true;
        }
        // Check if the status is the next status in workflow
        if ($newStatus - 1 === $oldStatus) {
            return true;
        }
        return false;
    }
}
