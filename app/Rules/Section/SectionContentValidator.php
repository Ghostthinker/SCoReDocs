<?php

namespace App\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use Illuminate\Support\Facades\Auth;

class SectionContentValidator extends SectionValidator
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute  The attribute name
     * @param  int  $value  The new content
     *
     * @return bool Indicates if content change is valid
     */
    public function passes($attribute, $value)
    {
        if (!($attribute === 'title' || $attribute === 'content')) {
            return true;
        }
        if (!$this->hasChanges($attribute, $value)) {
            return true;
        }
        if (!$this->entitledToChangeContentByHeading($this->getSection()->heading)) {
            $contentType = $attribute == 'content' ? ' den Inhalt ' : ' die Überschrift ';
            $this->setMessage('Fehlende Berechtigung um'.$contentType.'eines Abschnittes mit Überschrift 1 oder 2 zu ändern.');
            return false;
        }
        if (!$this->entitledToChangeContentByStatus($this->getSection()->status)) {
            $contentType = $attribute == 'content' ? ' den Inhalt ' : ' die Überschrift ';
            $this->setMessage('Fehlende Berechtigung um'.$contentType.'eines Abschnittes im Status "'. SectionStatus::getDescription($this->getSection()->status). '" zu ändern.');
            return false;
        }
        if (!$this->childrenAreNotLockedByParent($this->getSection())) {
            $this->setMessage('Unterabschnitte von Abschnitten die sich im Status
            "Bearbeitung nicht möglich" befindet, dürfen nicht bearbeitet werden.');
            return false;
        }
        return true;
    }

    /**
     * Checks if user is entitled to change the content or title
     *
     * @param  Section  $section  The section of the content
     *
     * @return bool Indicator if user is entitled
     */
    public static function entitledToChangeContent($section)
    {
        return self::entitledToChangeContentByHeading($section->heading)
            && self::entitledToChangeContentByStatus($section->status);
    }

    /**
     * Indicates if the section is locked by its parent
     *
     * @param  Section  $section  The section to check
     *
     * @return bool Indicator if section is locked or not
     */
    public static function childrenAreNotLockedByParent($section)
    {
        if (Auth::getUser()->can(PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT)) {
            return true;
        }
        $parentSection = last($section->getParents());
        if (!$parentSection) {
            return true;
        }
        if ($parentSection->status !== SectionStatus::EDIT_NOT_POSSIBLE) {
            return true;
        }
        return false;
    }

    /**
     * Checks if user is entitled to change the content depending on the status
     *
     * @param  int  $status  The status to change
     *
     * @return bool Indicator if user is entitled
     */
    public static function entitledToChangeContentByStatus($status)
    {
        if ($status == SectionStatus::EDIT_NOT_POSSIBLE) {
            return Auth::getUser()->can(PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT);
        }
        if ($status == SectionStatus::IN_REVIEW) {
            return Auth::getUser()->can(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW);
        }
        if ($status == SectionStatus::COMPLETED) {
            return Auth::getUser()->can(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED);
        }
        return true;
    }

    /**
     * Checks if user is entitled to change the content depending on the heading
     *
     * @param  int  $heading  The heading to change
     *
     * @return bool Indicator if user is entitled
     */
    private static function entitledToChangeContentByHeading($heading)
    {
        if ($heading > 2) {
            return true;
        }
        if ($heading == 1) {
            return Auth::getUser()->can(PermissionSet::CHANGE_HEADING_1_CONTENT);
        }
        if ($heading == 2) {
            return Auth::getUser()->can(PermissionSet::CHANGE_HEADING_2_CONTENT);
        }
        return false;
    }
}
