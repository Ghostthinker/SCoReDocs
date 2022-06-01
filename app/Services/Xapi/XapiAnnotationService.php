<?php

namespace App\Services\Xapi;

use App\Enums\XapiActivityDescription;
use App\Enums\XapiActivityType;
use App\Enums\XapiVerb;
use App\Facades\Xapi;
use App\Models\EP5\Annotation;
use Illuminate\Http\Request;

class XapiAnnotationService
{
    /**
     * @param Request $request
     * @param Annotation $annotation
     * @param array $data
     */
    public static function storeAnnotation(Request $request, Annotation $annotation, array $data)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_CREATED),
            $request->fullUrl(),
            ['en-US' => 'Video comment'],
            null,
            [url('/commentId') => $annotation->id,
                url('/videoId') => $data['video_nid'],
                url('/comment') => $data['body'],
                url('/timestamp') => $data['timestamp'],
                url('/revisionId') => $annotation->audits()->latest()->first()->id,
            ]);
    }

    /**
     * @param Request $request
     * @param array $data
     */
    public static function updateAnnotation(Request $request, array $data, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::UPDATED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_UPDATED),
            $request->fullUrl(),
            ['en-US' => 'Video comment'],
            null,
            [url('/commentId') => $request->input('id'),
                url('/videoId') => $data['video_nid'],
                url('/comment') => $data['body'],
                url('/timestamp') => $data['timestamp'],
                url('/revisionId') => $annotation->audits()->latest()->first()->id,
            ]);
    }

    /**
     * @param Request $request
     * @param Annotation $annotation
     */
    public static function destroyAnnotation(Request $request, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_DELETED),
            $request->fullUrl(),
            ['en-US' => 'Video comment'],
            null,
            [url('/commentId') => $annotation->id,
                url('/videoId') => $annotation->video_nid,
                url('/revisionId') => $annotation->audits()->latest()->first()->id,
            ]);
    }

    public static function addReplyAnnotation(Request $request, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::CREATED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_CREATED),
            $request->fullUrl(),
            ['en-US' => 'Video re-comment'],
            null,
            [url('/commentId') => $annotation->id,
                url('/videoId') => $annotation->video_nid,
                url('/comment') => $annotation->body,
                url('/parentComment') => $annotation->parent_id,
                url('/revisionId') => $annotation->audits()->latest()->first()->id
            ]);
    }

    public static function updateReplyAnnotation(Request $request, int $replyId, int $mediaId, int $parentId, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::UPDATED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_UPDATED),
            $request->fullUrl(),
            ['en-US' => 'Video re-comment'],
            null,
            [url('/commentId') => $replyId,
                url('/videoId') => $mediaId,
                url('/comment') => $request->only('body')['body'],
                url('/parentComment') => $parentId,
                url('/revisionId') => $annotation->audits()->latest()->first()->id
            ]);
    }

    public static function destroyReplyAnnotation(Request $request, int $replyId, int $mediaId, int $parentId, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::DELETED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_DELETED),
            $request->fullUrl(),
            ['en-US' => 'Video re-comment'],
            null,
            [url('/commentId') => $replyId,
                url('/videoId') => $mediaId,
                url('/parentComment') => $parentId,
                url('/revisionId') => $annotation->audits()->latest()->first()->id
            ]);
    }

    public static function insertedAnnotation(Request $request, Annotation $annotation)
    {
        Xapi::createStatement(
            new XapiVerb(XapiVerb::INSERTED),
            new XapiActivityType(XapiActivityType::COMMENT),
            new XapiActivityDescription(XapiActivityDescription::COMMENT_INSERTED),
            $request->fullUrl(),
            ['en-US' => 'Video comment'],
            null,
            [url('/commentId') => $annotation->id,
                url('/videoId') => $annotation->video_nid,
                url('/comment') => $annotation->body,
                url('/timestamp') => $annotation->timestamp,
                url('/revisionId') => $annotation->audits()->latest()->first()->id,
            ]);
    }
}
