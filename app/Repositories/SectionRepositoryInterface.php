<?php

namespace App\Repositories;

use App\Models\Section;

interface SectionRepositoryInterface
{
    /**
     * Get's a Section by it's ID
     *
     * @param int
     */
    public function get($sectionId);

    /**
     * @param $sectionId
     *
     * @return mixed
     */
    public function findOrFail($sectionId);

    /**
     * Get's a trashed Section by it's ID
     *
     * @param int
     */
    public function withTrashed($sectionId);

    /**
     * @param $projectId
     *
     * @return mixed
     */
    public function getAllByProjectId($projectId);

    /**
     * @param $projectId
     * @return mixed
     */
    public function getAllByProjectIdWithProjectAndUserCollapse($projectId);

    /**
     * @param $projectId
     *
     * @return mixed
     */
    public function getAllByProjectIdWithProjectPaginate($projectId);

    /**
     * Get's all Sections.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes a Section.
     *
     * @param int
     */
    public function delete($sectionId);

    /**
     * Updates a Section.
     *
     * @param  Section  $section The section model to update
     * @param  array  $data The new data as array
     *
     * @return bool The indicator if operation was successful
     */
    public function update($section, array $data);

    /**
     * Create a Section.
     *
     * @param array $data
     */
    public function create(array $data);
}
