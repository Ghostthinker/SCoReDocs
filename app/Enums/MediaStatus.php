<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Created()
 * @method static static Pending()
 * @method static static Converted()
 * @method static static Failed()
 */
class MediaStatus extends Enum
{
    public const CREATED = 0;
    public const PENDING = 1;
    public const CONVERTED = 2;
    public const FAILED_CONVERT = 3;
    public const FAILED_UPLOAD = 4;

    public static function getStatusByString(string $value) {
        switch ($value) {
            case 'converted':
                return self::CONVERTED;
            case 'failed':
                return self::FAILED_CONVERT;
            case 'created':
                return self::CREATED;
            case 'converting':
                return self::PENDING;
            default:
                return null;
        }
    }
}
