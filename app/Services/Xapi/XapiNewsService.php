<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Models\News;

class XapiNewsService
{

    public static function createNews(string $objectId, News $news)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::NEWS),
            new XapiActivityDescription(XapiActivityDescription::NEWS_CREATED),
            $objectId,
            ['en-US' => $news->title],
            null,
            [
                url('/newsId') => $news->id,
            ]
        );
    }

    public static function updateNews(string $objectId, News $news)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::UPDATED),
            new XapiActivityType(XapiActivityType::NEWS),
            new XapiActivityDescription(XapiActivityDescription::NEWS_UPDATED),
            $objectId,
            ['en-US' => $news->title],
            null,
            [
                url('/newsId') => $news->id,
            ]
        );
    }

    public static function deleteNews(string $objectId, News $news)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::NEWS),
            new XapiActivityDescription(XapiActivityDescription::NEWS_DELETED),
            $objectId,
            ['en-US' => $news->title],
            null,
            [
                url('/newsId') => $news->id,
            ]
        );
    }

    public static function readNews(string $objectId, News $news)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::READ),
            new XapiActivityType(XapiActivityType::NEWS),
            new XapiActivityDescription(XapiActivityDescription::NEWS_READ),
            $objectId,
            ['en-US' => $news->title]
        );
    }

}
