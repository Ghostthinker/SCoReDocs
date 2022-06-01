<?php

namespace App\Rules\Section;

use App\Rules\PermissionSet;
use Illuminate\Support\Facades\Auth;

class SectionDeleteValidator extends SectionValidator
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string   $attribute  The attribute name
     * @param  int      $value      The new content
     *
     * @return bool Indicates if content change is valid
     */
    public function passes($attribute, $value)
    {
        if ($attribute !== 'delete') {
            return false;
        }
        if (!$this->canDeleteLocked()) {
            return false;
        }
        if (!$this->canDeleteHeading()) {
            return false;
        }
        return true;
    }

    public function canDeleteHeading()
    {
        $heading = $this->getSection()->heading;
        if ($heading > 2) {
            return true;
        }
        if ($heading < 2) {
            return Auth::getUser()->can(PermissionSet::CAN_DELETE_SECTIONS_HEADING_1);
        }
        return Auth::getUser()->can(PermissionSet::CAN_DELETE_SECTIONS_HEADING_2);
    }

    public function canDeleteLocked()
    {
        if ($this->getSection()->locked != true) {
            return true;
        }
        return Auth::getUser()->can(PermissionSet::CAN_DELETE_SECTIONS_LOCKED);
    }
}
