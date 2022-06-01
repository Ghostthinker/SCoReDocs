<?php

namespace App\Repositories;

interface TimeoutRepositoryInterface
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
     * @param       $object
     * @param array $data The new data as array
     *
     * @return bool The indicator if operation was successful
     */
    public function update($object, array $data);

    /**
     * Create a Object.
     *
     * @param array $data
     */
    public function create(array $data);

    /**
     * Get a Object by sectionId.
     *
     * @param int $sectionId
     */
    public function getBySectionId(int $sectionId);

    /**
     * Update the timestamp of a Object by sectionId.
     *
     * @param int $sectionId
     */
    public function updateTimeout(int $sectionId);

    /**
     * Create a Object by sectionId.
     *
     * @param int $sectionId
     */
    public function createBySectionId(int $sectionId);

    /**
     * Delete a Object by sectionId.
     *
     * @param int $sectionId
     * @throws \Exception
     */
    public function deleteBySectionId(int $sectionId);
}
