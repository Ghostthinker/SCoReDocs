<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class ProjectStatus extends Enum
{
    public const IN_PROGRESS = 1;
    public const SUBMITTED = 2;
    public const IN_REVIEW = 3;
    public const COMPLETED = 4;

    public static function getDescription($value): string
    {
        if ($value === self::IN_PROGRESS) {
            return 'In Bearbeitung';
        }

        if ($value === self::SUBMITTED) {
            return 'Eingereicht';
        }

        if ($value === self::IN_REVIEW) {
            return 'In Prüfung';
        }

        if ($value === self::COMPLETED) {
            return 'Abgeschlossen';
        }

        return parent::getDescription($value);
    }
}
