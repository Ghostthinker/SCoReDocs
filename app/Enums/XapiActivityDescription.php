<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class XapiActivityDescription extends Enum
{
    /** SECTION **/
    public const SECTION_CREATED = 0;
    public const SECTION_UPDATED = 1;
    public const SECTION_VIEWED = 2;
    public const SECTION_STARTED_EDIT = 3;
    public const SECTION_CANCELED_EDIT = 4;
    public const SECTION_REVERTED_VERSION = 5;
    public const SECTION_COMPARED_VERSIONS = 6;
    public const SECTION_VIEWED_HISTORY = 7;
    public const SECTION_REVERTED = 8;
    public const SECTION_DELETED = 9;
    public const SECTION_CLICKED_MINOR = 101;
    public const SECTION_EXPANDED = 10;
    public const SECTION_COLLAPSED = 11;

    /** PROJECT **/
    public const PROJECT_CREATED = 200;
    public const PROJECT_OPENED = 201;
    public const PROJECT_LEFT = 202;
    public const PROJECT_READ_ALL = 203;

    /** VIDEO **/
    public const VIDEO_CREATED = 13;
    public const VIDEO_DELETED = 14;

    /** IMAGE **/
    public const IMAGE_CREATED = 15;
    public const IMAGE_DELETED = 16;

    /** COMMENT **/
    public const COMMENT_CREATED = 17;
    public const COMMENT_DELETED = 18;
    public const COMMENT_UPDATED = 19;
    public const COMMENT_INSERTED = 20;


    /** LINK **/
    public const LINK_CREATED = 21;
    public const LINK_DELETED = 22;

    public const MEDIA_CREATED = 30;
    public const MEDIA_OPENED = 31;
    public const MEDIA_PLAYED = 32;
    public const MEDIA_PAUSED = 33;
    public const MEDIA_SEEKED = 34;
    public const MEDIA_ENDED = 35;
    public const MEDIA_LEFT = 36;
    public const MEDIA_UPLOADED = 37;

    /** CHAT */
    public const CHAT_TYPED = 40;
    public const CHAT_SENT = 41;
    public const CHAT_OPENED = 42;
    public const CHAT_LEFT = 43;
    public const CHAT_SWITCHED = 44;
    public const CHAT_MENTIONED = 45;
    public const CHAT_OPENED_MENTIONING = 46;
    public const CHAT_MENTIONED_ALL = 47;

    /** Playback **/
    public const PLAYBACK_COMMAND_CREATED = 50;
    public const PLAYBACK_COMMAND_UPDATED = 51;
    public const PLAYBACK_COMMAND_DELETED = 52;

    /** SEQUENCE **/
    public const SEQUENCE_CREATED = 60;
    public const SEQUENCE_UPDATED = 61;

    /**  PLAYLIST **/
    public const PLAYLIST_OPENED = 70;
    public const PLAYLIST_DOWNLOADED = 71;

    /**  ACTIVITY **/
    public const ACTIVITY_VIEWED = 80;
    public const ACTIVITY_READ = 81;

    /** MAIL LINK **/
    public const MAIL_LINK_VIEWED = 90;

    /** NEWS **/
    public const NEWS_CREATED = 91;
    public const NEWS_UPDATED = 92;
    public const NEWS_DELETED = 93;
    public const NEWS_READ = 94;

    public static function getDesc($value): array
    {
        /** SECTION **/
        if ($value === self::SECTION_CREATED) {
            return ['en-US' => 'A section was created'];
        }
        if ($value === self::SECTION_UPDATED) {
            return ['en-US' => 'A section was updated'];
        }
        if ($value === self::SECTION_VIEWED) {
            return ['en-US' => 'A section was viewed'];
        }
        if ($value === self::SECTION_STARTED_EDIT) {
            return ['en-US' => 'Edit on a section was started'];
        }
        if ($value === self::SECTION_CANCELED_EDIT) {
            return ['en-US' => 'Edit on a section was canceled'];
        }
        if ($value === self::SECTION_REVERTED_VERSION) {
            return ['en-US' => 'A section was reverted to another version'];
        }
        if ($value === self::SECTION_COMPARED_VERSIONS) {
            return ['en-US' => 'Two versions of a section were compared'];
        }
        if ($value === self::SECTION_VIEWED_HISTORY) {
            return ['en-US' => 'The history of a section was viewed'];
        }
        if ($value === self::SECTION_REVERTED) {
            return ['en-US' => 'A section was reverted'];
        }
        if ($value === self::SECTION_DELETED) {
            return ['en-US' => 'A section was deleted'];
        }
        if ($value === self::SECTION_CLICKED_MINOR) {
            return ['en-US' => 'Minor update of a section was clicked'];
        }
        if($value === self::SECTION_EXPANDED) {
            return ['en-US' => 'A section was expanded'];
        }
        if($value === self::SECTION_COLLAPSED) {
            return ['en-US' => 'A section was collapsed'];
        }

        /** PROJECT **/
        if ($value === self::PROJECT_CREATED) {
            return ['en-US' => 'A project was created'];
        }
        if ($value === self::PROJECT_OPENED) {
            return ['en-US' => 'Project was opened'];
        }
        if ($value === self::PROJECT_LEFT) {
            return ['en-US' => 'Project was left'];
        }
        if ($value === self::PROJECT_READ_ALL) {
            return ['en-US' => 'All messages and activities were read in project'];
        }

        /** VIDEO **/
        if ($value === self::VIDEO_CREATED) {
            return ['en-US' => 'A video was created'];
        }
        if ($value === self::VIDEO_DELETED) {
            return ['en-US' => 'A video was deleted'];
        }

        /** IMAGE **/
        if ($value === self::IMAGE_CREATED) {
            return ['en-US' => 'An image was created'];
        }
        if ($value === self::IMAGE_DELETED) {
            return ['en-US' => 'An image was deleted'];
        }

        /** COMMENT **/
        if ($value === self::COMMENT_CREATED) {
            return ['en-US' => 'A comment was created'];
        }
        if ($value === self::COMMENT_DELETED) {
            return ['en-US' => 'A comment was deleted'];
        }
        if ($value === self::COMMENT_UPDATED) {
            return ['en-US' => 'A comment was updated'];
        }
        if ($value === self::COMMENT_INSERTED) {
            return ['en-US' => 'A comment was inserted'];
        }

        /** LINK **/
        if ($value === self::LINK_CREATED) {
            return ['en-US' => 'A link was created'];
        }
        if ($value === self::LINK_DELETED) {
            return ['en-US' => 'A link was deleted'];
        }

        if ($value === self::PLAYBACK_COMMAND_CREATED) {
            return ['en-US' => 'A video script was created'];
        }
        if ($value === self::PLAYBACK_COMMAND_UPDATED) {
            return ['en-US' => 'A video script was updated'];
        }
        if ($value === self::PLAYBACK_COMMAND_DELETED) {
            return ['en-US' => 'A video script was deleted'];
        }

        if ($value === self::MEDIA_CREATED) {
            return ['en-US' => 'A media was created'];
        }
        if ($value === self::MEDIA_OPENED) {
            return ['en-US' => 'A media was opened'];
        }
        if ($value === self::MEDIA_PLAYED) {
            return ['en-US' => 'A media was played'];
        }
        if ($value === self::MEDIA_PAUSED) {
            return ['en-US' => 'A media was paused'];
        }
        if ($value === self::MEDIA_SEEKED) {
            return ['en-US' => 'A media was seeked'];
        }
        if ($value === self::MEDIA_ENDED) {
            return ['en-US' => 'A media has ended'];
        }
        if ($value === self::MEDIA_LEFT) {
            return ['en-US' => 'A media was left'];
        }
        if ($value === self::MEDIA_UPLOADED) {
            return ['en-US' => 'A media was uploaded'];
        }

        /** CHAT **/
        if ($value === self::CHAT_TYPED) {
            return ['en-US' => 'A message was typed'];
        }
        if ($value === self::CHAT_SENT) {
            return ['en-US' => 'A message was sent'];
        }
        if ($value === self::CHAT_OPENED) {
            return ['en-US' => 'A chat was opened'];
        }
        if ($value === self::CHAT_LEFT) {
            return ['en-US' => 'A chat was left'];
        }
        if ($value === self::CHAT_SWITCHED) {
            return ['en-US' => 'A chat was switched'];
        }
        if ($value === self::CHAT_MENTIONED) {
            return ['en-US' => 'A user was mentioned'];
        }
        if ($value === self::CHAT_OPENED_MENTIONING) {
            return ['en-US' => 'A user opened a mentioning'];
        }
        if ($value === self::CHAT_MENTIONED_ALL) {
            return ['en-US' => 'All users were mentioned'];
        }

        /** SEQUENCE */
        if ($value === self::SEQUENCE_CREATED) {
            return ['en-US' => 'A videosequence was created'];
        }

        if ($value === self::SEQUENCE_UPDATED) {
            return ['en-US' => 'A videosequence was updated'];
        }

        /** PLAYLIST **/
        if ($value === self::PLAYLIST_OPENED) {
            return ['en-US' => 'A playlist was opened'];
        }
        if ($value === self::PLAYLIST_DOWNLOADED) {
            return ['en-US' => 'A playlist was downloaded'];
        }

        /** ACTIVITY **/
        if ($value === self::ACTIVITY_VIEWED) {
            return ['en-US' => 'A activity was viewed'];
        }

        if ($value === self::ACTIVITY_READ) {
            return ['en-US' => 'An activity was read'];
        }

        /** MAIL LINK **/
        if ($value === self::MAIL_LINK_VIEWED) {
            return ['en-US' => 'A mail link was viewed'];
        }

        /** NEWS **/
        if ($value === self::NEWS_CREATED) {
            return ['en-US' => 'A news has been created'];
        }

        if ($value === self::NEWS_UPDATED) {
            return ['en-US' => 'A news has been updated'];
        }

        if ($value === self::NEWS_DELETED) {
            return ['en-US' => 'A news has been deleted'];
        }

        if ($value === self::NEWS_READ) {
            return ['en-US' => 'A news has been read'];
        }
    }
}
