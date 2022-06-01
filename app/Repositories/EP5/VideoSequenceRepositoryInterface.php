<?php

namespace App\Repositories\EP5;

use App\User;

interface VideoSequenceRepositoryInterface
{
    /**
     * Get's a post by it's ID
     *
     * @param int
     */
    public function get($videoSequenceId);

    /**
     * Get's a post by it's ID with user
     *
     * @param int
     */
    public function getWithUser($videoSequenceId);

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
    public function delete($videoSequenceId);

    /**
     * Updates an instruction.
     *
     * @param int
     * @param array
     */
    public function update($videoSequenceId, array $data);

    /**
     * Create an instruction.
     *
     * @param array
     * @param User|null $user
     */
    public function create(array $data, $user = null);

    public function getByMediaId($media_id);
}
