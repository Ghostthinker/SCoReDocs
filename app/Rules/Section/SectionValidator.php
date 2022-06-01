<?php

namespace App\Rules\Section;

use App\Models\Project;
use App\Models\Section;
use Illuminate\Contracts\Validation\Rule;

abstract class SectionValidator implements Rule
{
    private $message = 'Abschnitt konnte nicht verändert werden. Es fehlen nötige Berechtigungen!';
    private $section = null;
    private $project = null;

    /**
     * Create a new rule instance.
     *
     * @param Section $section The section to validate
     * @param null $project Parentproject of the section
     */
    public function __construct($section, $project = null)
    {
        $this->section = $section;
        $this->project = $project;
    }

    /**
     * Get the validation error message.
     *
     * @return string The error message
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Indicator if attribute has changed
     *
     * @param  string  $attribute  The attribute to change as string
     * @param  mixed  $value  The value to change the attribute to
     *
     * @return bool The indicator if section attribute was changed
     */
    public function hasChanges($attribute, $value)
    {
        return $this->section[$attribute] != $value;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param  string  $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return Section
     */
    public function getSection(): Section
    {
        return $this->section;
    }

    /**
     * @param  Section  $section
     */
    public function setSection(Section $section): void
    {
        $this->section = $section;
    }

    /**
     * @return Project|null
     */
    public function getProject()
    {
        return $this->project;
    }
}
