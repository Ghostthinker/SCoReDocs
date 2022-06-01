<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use Auth;

class XapiChatService
{
    public static function typing($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::TYPED,
            XapiActivityDescription::CHAT_TYPED,
            $parent, $data, $projectId
        );
    }

    public static function send($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::SENT,
            XapiActivityDescription::CHAT_SENT,
            $parent, $data, $projectId,
            [
                url('/messageId') => $data['message_id']
            ]
        );
    }

    public static function open($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::OPENED,
            XapiActivityDescription::CHAT_OPENED,
            $parent, $data, $projectId
        );
    }

    public static function left($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::LEFT,
            XapiActivityDescription::CHAT_LEFT,
            $parent, $data, $projectId
        );
    }

    public static function switched($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::SWITCHED,
            XapiActivityDescription::CHAT_SWITCHED,
            $parent, $data, $projectId,
            [
                url('/oldChatId') => $data['oldChatId'],
                url('/oldChatType') => $data['oldChatType'],
                url('/newChatId') => $data['newChatId'],
                url('/newChatType') => $data['newChatType'],
            ]
        );
    }

    public static function mentioned($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::MENTIONED,
            XapiActivityDescription::CHAT_MENTIONED,
            $parent, $data, $projectId, [
                url('/mentioningTargetUser') => $data['mentionedUser'],
                url('/mentioningSourceUser') => Auth::id(),
            ]
        );
    }

    public static function mentionedAll($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::MENTIONED,
            XapiActivityDescription::CHAT_MENTIONED_ALL,
            $parent, $data, $projectId, [
                url('/mentioningSourceUser') => Auth::id(),
            ]
        );
    }

    public static function openedMentioning($parent, $data, $projectId)
    {
        self::createStatement(
            XapiVerb::OPENED,
            XapiActivityDescription::CHAT_OPENED_MENTIONING,
            $parent, $data, $projectId, [
                url('/messageId') => $data['messageId']
            ]
        );
    }

    /**
     * @param $verb
     * @param $description
     * @param $parent
     * @param $data
     * @param $projectId
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    private static function createStatement($verb, $description, $parent, $data, $projectId, $extensions = null): void
    {
        $parentId = $data['parentId'];
        $type = strrpos($parentId, 'section') !== false ? XapiActivityType::SECTION : XapiActivityType::PROJECT;
        $parentTitle = $parent !== null ? ['en-US' => $parent->title] : ['en-US' => ''];
        Xapi::createStatement(
            new XapiVerb($verb),
            new XapiActivityType(XapiActivityType::MESSAGE),
            new XapiActivityDescription($description),
            $data['fullUrl'],
            $data['title'],
            $projectId,
            $extensions,
            $parentId,
            $parentTitle,
            new XapiActivityType($type)
        );
    }
}
