<?php

namespace App\Repositories;

use App\Models\Media;

interface MediaRepositoryInterface
{
    /**
     * Get's a Object by it's ID
     *
     * @param  int
     */
    public function get($id);

    /**
     * Get's media with it's user
     *
     * @param  int
     */
    public function getWithUser($id);

    /**
     * Get's a Object by it's ID
     *
     * @param  Media
     */
    public function save($media);

    /**
     * Get's all Objects.
     *
     * @return mixed
     */
    public function getAll();

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findOrFail($id);

    /**
     * @param array $data
     *
     * @param null $user
     * @return mixed
     */
    public function create(array $data, $user = null);

    /**
     * Returns the media by their status
     *
     * @param int | int[] $status
     *
     * @return Media[] The media array
     */
    public function getMediaByStatus($status);
}
