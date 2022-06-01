<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
class XapiVerb extends Enum
{
    public const CREATED = 0;
    public const OPENED = 1;
    public const LEFT = 2;
    public const UPDATED = 3;
    public const VIEWED = 4;
    public const DELETED = 5;
    public const STARTED_EDIT = 6;
    public const CANCELED_EDIT = 7;
    public const REVERTED_VERSION = 8;
    public const COMPARED_VERSIONS = 9;
    public const VIEWED_HISTORY = 10;
    public const INSERTED = 11;
    public const PLAYED = 12;
    public const PAUSED = 13;
    public const ENDED = 14;
    public const SEEKED = 15;
    public const TYPED = 16;
    public const SENT = 17;
    public const UPLOADED = 18;
    public const SWITCHED = 19;
    public const REVERTED = 20;
    public const MENTIONED = 21;
    public const DOWNLOADED = 22;
    public const CLOSED = 23;
    public const CLICKED = 24;
    public const COLLAPSED = 25;
    public const EXPANDED = 26;
    public const READ = 27;
    public const READ_ALL = 28;

    public static function getDescription($value): string
    {
        if ($value === self::CREATED) {
            return 'http://activitystrea.ms/schema/1.0/create';
        }
        if ($value === self::OPENED) {
            return 'http://activitystrea.ms/schema/1.0/open';
        }
        if ($value === self::LEFT) {
            return 'http://activitystrea.ms/schema/1.0/leave';
        }
        if ($value === self::UPDATED) {
            return 'http://activitystrea.ms/schema/1.0/update';
        }
        if ($value === self::VIEWED) {
            return 'http://activitystrea.ms/schema/1.0/viewed';
        }
        if ($value === self::PLAYED) {
            return 'http://activitystrea.ms/schema/1.0/played';
        }
        if ($value === self::PAUSED) {
            return 'http://activitystrea.ms/schema/1.0/stopped';
        }
        if ($value === self::ENDED) {
            return 'http://activitystrea.ms/schema/1.0/ended';
        }
        if ($value === self::SEEKED) {
            return 'http://activitystrea.ms/schema/1.0/seeked';
        }
        if ($value === self::DELETED) {
            return 'http://activitystrea.ms/schema/1.0/deleted';
        }
        if ($value === self::STARTED_EDIT) {
            return 'http://activitystrea.ms/schema/1.0/started_edit';
        }
        if ($value === self::CANCELED_EDIT) {
            return 'http://activitystrea.ms/schema/1.0/canceled_edit';
        }
        if ($value === self::REVERTED_VERSION) {
            return 'http://activitystrea.ms/schema/1.0/reverted_version';
        }
        if ($value === self::COMPARED_VERSIONS) {
            return 'http://activitystrea.ms/schema/1.0/compared_versions';
        }
        if ($value === self::VIEWED_HISTORY) {
            return 'http://activitystrea.ms/schema/1.0/viewed_history';
        }
        if ($value === self::INSERTED) {
            return 'http://activitystrea.ms/schema/1.0/insert';
        }
        if ($value === self::TYPED) {
            return 'http://activitystrea.ms/schema/1.0/typed';
        }
        if ($value === self::SENT) {
            return 'http://activitystrea.ms/schema/1.0/sent';
        }
        if ($value === self::UPLOADED) {
            return 'http://activitystrea.ms/schema/1.0/uploaded';
        }
        if ($value === self::SWITCHED) {
            return 'http://activitystrea.ms/schema/1.0/switched';
        }
        if ($value === self::REVERTED) {
            return 'http://activitystrea.ms/schema/1.0/reverted';
        }
        if ($value === self::DOWNLOADED) {
            return 'http://activitystrea.ms/schema/1.0/downloaded';
        }
        if ($value === self::MENTIONED) {
            return 'http://activitystrea.ms/schema/1.0/mentioned';
        }
        if ($value === self::CLICKED) {
            return 'http://activitystrea.ms/schema/1.0/clicked';
        }
        if ($value === self::CLOSED) {
            return 'http://activitystrea.ms/schema/1.0/closed';
        }
        if ($value === self::COLLAPSED) {
            return 'http://activitystrea.ms/schema/1.0/collapsed';
        }
        if ($value === self::EXPANDED) {
            return 'http://activitystrea.ms/schema/1.0/expanded';
        }
        if ($value === self::READ) {
            return 'http://activitystrea.ms/schema/1.0/read';
        }
        if ($value === self::READ_ALL) {
            return 'http://activitystrea.ms/schema/1.0/read_all';
        }
    }

    public static function getDisplayName($value): array
    {
        if ($value === self::CREATED) {
            return ['en-US' => 'created'];
        }
        if ($value === self::OPENED) {
            return ['en-US' => 'opened'];
        }
        if ($value === self::LEFT) {
            return ['en-US' => 'left'];
        }
        if ($value === self::UPDATED) {
            return ['en-US' => 'updated'];
        }
        if ($value === self::VIEWED) {
            return ['en-US' => 'viewed'];
        }
        if ($value === self::PLAYED) {
            return ['en-US' => 'played'];
        }
        if ($value === self::PAUSED) {
            return ['en-US' => 'paused'];
        }
        if ($value === self::ENDED) {
            return ['en-US' => 'ended'];
        }
        if ($value === self::SEEKED) {
            return ['en-US' => 'seeked'];
        }
        if ($value === self::DELETED) {
            return ['en-US' => 'deleted'];
        }
        if ($value === self::STARTED_EDIT) {
            return ['en-US' => 'started editing of'];
        }
        if ($value === self::CANCELED_EDIT) {
            return ['en-US' => 'canceled editing of'];
        }
        if ($value === self::REVERTED_VERSION) {
            return ['en-US' => 'reverted version of'];
        }
        if ($value === self::COMPARED_VERSIONS) {
            return ['en-US' => 'compared versions of'];
        }
        if ($value === self::VIEWED_HISTORY) {
            return ['en-US' => 'viewed history of'];
        }
        if ($value === self::INSERTED) {
            return ['en-US' => 'inserted'];
        }
        if ($value === self::TYPED) {
            return ['en-US' => 'typed'];
        }
        if ($value === self::SENT) {
            return ['en-US' => 'sent'];
        }
        if ($value === self::UPLOADED) {
            return ['en-US' => 'uploaded'];
        }
        if ($value === self::SWITCHED) {
            return ['en-US' => 'switched'];
        }
        if ($value === self::REVERTED) {
            return ['en-US' => 'reverted'];
        }
        if ($value === self::DOWNLOADED) {
            return ['en-US' => 'downloaded'];
        }
        if ($value === self::MENTIONED) {
            return ['en-US' => 'mentioned'];
        }
        if ($value === self::CLICKED) {
            return ['en-US' => 'clicked'];
        }
        if ($value === self::CLOSED) {
            return ['en-US' => 'closed'];
        }
        if ($value === self::COLLAPSED) {
            return ['en-US' => 'collapsed'];
        }
        if ($value === self::EXPANDED) {
            return ['en-US' => 'expanded'];
        }
        if ($value === self::READ) {
            return ['en-US' => 'read'];
        }
        if ($value === self::READ_ALL) {
            return ['en-US' => 'read all messages and activities of'];
        }
    }
}
