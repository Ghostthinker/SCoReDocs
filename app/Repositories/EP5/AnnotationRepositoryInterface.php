<?php

namespace App\Repositories\EP5;

use App\User;

interface AnnotationRepositoryInterface
{
    /**
     * Get's a post by it's ID
     *
     * @param int
     */
    public function get($annotationId);

    public function getWithReply($annotationId);

    public function getByMediaId($mediaId);

    public function getVersionsCount($annotationId);

    public function getBySequenceId($sequenceId);

    /**
     * Get's all annotations.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes an annotation.
     *
     * @param int
     */
    public function delete($annotationId);

    /**
     * Updates an annotation.
     *
     * @param int
     * @param array
     */
    public function update($annotationId, array $data);

    /**
     * Create an annotation.
     *
     * @param array
     * @param User|null $user
     */
    public function create(array $data, $user = null);

    public function addReply(array $replyData);
}
