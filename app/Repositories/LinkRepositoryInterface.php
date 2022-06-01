<?php

namespace App\Repositories;

interface LinkRepositoryInterface
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
     * @param $id
     *
     * @return mixed
     */
    public function getByRefId($id);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getBySection($id);

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function deleteIds(array $input);

    /**
     * @param array $values
     *
     * @return mixed
     */
    public function insertRecordAndGetId(array $values);
}
