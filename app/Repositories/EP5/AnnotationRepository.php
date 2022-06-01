<?php

namespace App\Repositories\EP5;

use App\Models\EP5\Annotation;
use Arr;
use Exception;
use Throwable;

class AnnotationRepository implements AnnotationRepositoryInterface
{
    public function get($annotationId)
    {
        return Annotation::find($annotationId);
    }

    public function getWithReply($annotationId)
    {
        return Annotation::with('replies')->find($annotationId);
    }

    public function all()
    {
        return Annotation::all();
    }

    public function delete($annotationId)
    {
        Annotation::destroy($annotationId);
    }

    public function update($annotationId, array $data)
    {
        try {
            $annotation = Annotation::findOrFail($annotationId);
            $success = $annotation->update($data);
            if($success) {
                return $annotation;
            }else {
                false;
            }
        } catch (Throwable $exception) {
            return false;
        }
    }

    public function create(array $data, $user = null): Annotation
    {
        $annotation = Annotation::make($data);
        if ($user !== null) {
            $annotation->user()->associate($user);
        }
        if ($annotation->save()) {
            return $annotation;
        }
    }

    public function getByMediaId($mediaId)
    {
        return Annotation::with('replies')->byMediaId($mediaId)->get();
    }

    public function getBySequenceId($sequenceId)
    {
        return Annotation::with('replies')->bySequenceId($sequenceId)->get();
    }

    public function getVersionsCount($annotationId)
    {
        return Annotation::find($annotationId)->audits()->count();
    }

    public function addReply(array $replyData)
    {
        if (!Arr::has($replyData, 'parent_id')) {
            throw new Exception('ParentId required');
        }
        $parentAnnotation = Annotation::findOrFail($replyData['parent_id']);
        $annotation = Annotation::make($replyData);

        $annotation->timestamp = $parentAnnotation->timestamp;
        $annotation->video_nid = $parentAnnotation->video_nid;
        $annotation->parent_id = $parentAnnotation->id;
        $annotation->user_id = $replyData['user_id'];

        $annotation->save();
        return $annotation;
    }
}
