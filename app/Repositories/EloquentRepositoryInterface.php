<?php

namespace App\Repositories;

interface EloquentRepositoryInterface
{
    /**
     * Get's a Object by it's ID
     *
     * @param int
     */
    public function get($id);

    /**
     * Get's all Objects.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes a Object.
     *
     * @param int
     */
    public function delete($id);

    /**
     * Updates a Object.
     *
     * @param       $id
     * @param array $data The new data as array
     *
     * @return bool The indicator if operation was successful
     */
    public function update($id, array $data);

    /**
     * Create a Object.
     *
     * @param array $data
     */
    public function create(array $data);
}
