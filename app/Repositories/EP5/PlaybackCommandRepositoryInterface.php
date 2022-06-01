<?php

namespace App\Repositories\EP5;

use App\User;

interface PlaybackCommandRepositoryInterface
{
    /**
     * Get's a post by it's ID
     *
     * @param int
     */
    public function get($instructionId);

    /**
     * Get's all instruction.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes an instruction.
     *
     * @param int
     */
    public function delete($playbackId);

    /**
     * Updates an instruction.
     *
     * @param int
     * @param array
     */
    public function update($playbackId, array $data);

    /**
     * Create an instruction.
     *
     * @param array
     * @param User|null $user
     */
    public function create(array $data, $user = null);

    public function getByMediaId($media_id);
}
