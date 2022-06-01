<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class ProjectType extends Enum
{
    public const PROJECT = 'Project';
    public const PROJECT_TEMPLATE = 'Project Template';
    public const TEMPLATE = 'Template'; # Template for Assessment Doc
    public const ASSESSMENT_DOC = 'AssessmentDoc';
    public const ARCHIVED = 'Archived';
}
