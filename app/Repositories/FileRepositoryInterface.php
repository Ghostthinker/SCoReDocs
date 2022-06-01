<?php

namespace App\Repositories;

interface FileRepositoryInterface
{
    /**
     * Get's a Files by it's ID
     *
     * @param int
     */
    public function get($fileId);

    /**
     * @param $fileId
     *
     * @return mixed
     */
    public function getByName($fileId);

    /**
     * @param $filePath
     *
     * @return mixed
     */
    public function getByPath($filePath);

    /**
     * Get's all Files.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes a File.
     *
     * @param int
     */
    public function delete($fileId);

    /**
     * Updates a File.
     *
     * @param int
     * @param array
     */
    public function update($fileId, array $data);

    /**
     * Create a File.
     *
     * @param array
     */
    public function create(array $data);
}
