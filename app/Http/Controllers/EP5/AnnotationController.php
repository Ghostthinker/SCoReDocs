<?php

namespace App\Http\Controllers\EP5;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnotationReplyRequest;
use App\Http\Requests\AnnotationRequest;
use App\Models\EP5\Annotation;
use App\Repositories\EP5\AnnotationRepositoryInterface;
use App\Rules\PermissionSet;
use App\Services\Xapi\XapiAnnotationService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AnnotationController extends Controller
{
    /**
     * AnnotationController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($media_id, AnnotationRepositoryInterface $annotationRepository)
    {
        return $annotationRepository->getByMediaId($media_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\AnnotationRequest $request
     * @param \App\Repositories\EP5\AnnotationRepositoryInterface $annotationRepository
     * @return \Illuminate\Http\Response
     */
    public function store(AnnotationRequest $request, AnnotationRepositoryInterface $annotationRepository)
    {
        $data = $request->only(['body', 'timestamp', 'video_nid', 'drawing_data', 'rating', 'sequence_id', 'look_at']);

        $user = Auth::user();
        $data['userdata'] = [
            'name' => $user->name,
            'uid' => $user->id,
            'picture' => '/assets/images/default_user.png',
        ];

        if ($request->has('id')) {
            return $annotationRepository->update($request->input('id'), $data);
        }

        $annotation = $annotationRepository->create($data, $user);
        XapiAnnotationService::storeAnnotation($request, $annotation, $data);

        return $annotation;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\EP5\Annotation $annotation
     * @return \Illuminate\Http\Response
     */
    public function show(Annotation $annotation)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\EP5\Annotation $annotation
     * @return \Illuminate\Http\Response
     */
    public function edit(Annotation $annotation)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AnnotationRequest $request
     * @param Annotation $annotation
     * @param int $mediaId
     * @param int $annotationId
     * @param AnnotationRepositoryInterface $annotationRepository
     * @return Response
     */
    public function update(
        AnnotationRequest $request,
        int $mediaId,
        int $annotationId,
        AnnotationRepositoryInterface $annotationRepository
    ) {

        $annotation = Annotation::findOrFail($annotationId);
        $updateAccess = $annotation->userHasPermission('update');

        // Check if user is allowed to update the annotation
        if (!$updateAccess) {
            throw new AccessDeniedHttpException();
        }

        $data = $request->validationData();
        $success = $annotationRepository->update($request->input('id'), $data);
        XapiAnnotationService::updateAnnotation($request, $data, $annotation);
        return $annotationRepository->getWithReply($request->input('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $mediaId
     * @param int $annotationId
     * @param AnnotationRepositoryInterface $annotationRepository
     * @return Response
     */
    public function destroy(Request $request, int $mediaId, int $annotationId, AnnotationRepositoryInterface $annotationRepository)
    {
        $annotation = $annotationRepository->get($annotationId);
        $deleteAccess = $annotation->userHasPermission('delete');

        // Check if user is allowed to delete the annotation
        if (!$deleteAccess) {
            throw new AccessDeniedHttpException();
        }

        $success = $annotationRepository->delete($annotationId);
        XapiAnnotationService::destroyAnnotation($request, $annotation);
        return $success;
    }

    public function addReply(
        AnnotationReplyRequest $request,
        $mediaId,
        $parentId,
        AnnotationRepositoryInterface $annotationRepository
    ) {
        $user = Auth::user();

        $annotation = $annotationRepository->get($parentId);
        $replyAccess = $annotation->userHasPermission('reply');

        // check if user may create replies
        if (!$replyAccess) {
            throw new AccessDeniedHttpException();
        }

        $replyData = $request->validationData();
        $replyData['parent_id'] = $parentId;
        $replyData['user_id'] = $user->id;

        $annotation = $annotationRepository->addReply($replyData);
        XapiAnnotationService::addReplyAnnotation($request, $annotation);

        return $annotation;
    }

    public function updateReply(
        AnnotationReplyRequest $request,
        $mediaId,
        $parentId,
        $replyId,
        AnnotationRepositoryInterface $annotationRepository
    ) {

        $annotation = $annotationRepository->get($replyId);
        $replyUpdateAccess = $annotation->userHasPermission('update');

        // Check if user is allowed to update the annotation
        if (!$replyUpdateAccess) {
            throw new AccessDeniedHttpException();
        }

        $annotation = $annotationRepository->update($replyId, $request->only('body'));
        XapiAnnotationService::updateReplyAnnotation($request, $replyId, $mediaId, $parentId, $annotation);
        return $annotation;
    }

    /**
     * @param Request $request
     * @param $mediaId
     * @param $parentId
     * @param $replyId
     * @param AnnotationRepositoryInterface $annotationRepository
     * @return mixed
     */
    public function destroyReply(Request $request, int $mediaId, int $parentId, int $replyId, AnnotationRepositoryInterface $annotationRepository)
    {
        $annotation = $annotationRepository->get($replyId);
        $replyDeleteAccess = $annotation->userHasPermission('delete');

        // Check if user is allowed to delete the annotation
        if (!$replyDeleteAccess) {
            throw new AccessDeniedHttpException();
        }

        $success = $annotationRepository->delete($replyId);
        XapiAnnotationService::destroyReplyAnnotation($request, $replyId, $mediaId, $parentId, $annotation);
        return $success;
    }

    public function getCount(int $mediaId, AnnotationRepositoryInterface $annotationRepository)
    {
        return $annotationRepository->getByMediaId($mediaId)->count();
    }

    /**
     * @param  int  $mediaId
     * @param  int  $sequenceId
     * @param  AnnotationRepositoryInterface  $annotationRepository
     * @return mixed
     */
    public function getSequenceCount(int $mediaId, int $sequenceId, AnnotationRepositoryInterface $annotationRepository)
    {
        return $annotationRepository->getBySequenceId($sequenceId)->count();
    }
}
