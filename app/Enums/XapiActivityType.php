<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class XapiActivityType extends Enum
{
    public const PROJECT = 0;
    public const SECTION = 1;
    public const IMAGE = 2;
    public const VIDEO = 3;
    public const COMMENT = 4;
    public const LINK = 5;
    public const MESSAGE = 6;
    public const SEQUENCE = 7;
    public const PLAYLIST = 8;
    public const ACTIVITY = 9;
    public const NEWS = 10;

    /**
     * @param $value
     * @return string
     */
    public static function getActivityType($value): string
    {
        if ($value === self::PROJECT) {
            return 'http://id.tincanapi.com/activitytype/project';
        }
        if ($value === self::SECTION) {
            return 'http://id.tincanapi.com/activitytype/section';
        }
        if ($value === self::IMAGE) {
            return 'http://id.tincanapi.com/activitytype/image';
        }
        if ($value === self::VIDEO) {
            return 'http://id.tincanapi.com/activitytype/video';
        }
        if ($value === self::COMMENT) {
            return 'http://id.tincanapi.com/activitytype/comment';
        }
        if ($value === self::LINK) {
            return 'http://id.tincanapi.com/activitytype/link';
        }
        if ($value === self::MESSAGE) {
            return 'http://id.tincanapi.com/activitytype/message';
        }
        if ($value === self::SEQUENCE) {
           return  'http://id.tincanapi.com/activitytype/sequence';
        }
        if ($value === self::PLAYLIST) {
            return  'http://id.tincanapi.com/activitytype/playlist';
        }
        if ($value === self::ACTIVITY) {
            return  'http://id.tincanapi.com/activitytype/activity';
        }
        if ($value === self::NEWS) {
            return  'http://id.tincanapi.com/activitytype/news';
        }
    }
}
