<?php

namespace App\Repositories;

use App\Enums\ProjectType;
use App\Models\Project;

interface ProjectRepositoryInterface
{
    /**
     * Get's a Projects by it's ID
     *
     * @param int
     * @return Project
     */
    public function get($projectId);

    /**
     * Get's all Projects.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get's all projects of a given type
     *
     * @param ProjectType $type
     * @return mixed
     */
    public function allOfType(ProjectType $type);

    /**
     * Get's all assessment docs with its owner
     *
     * @return mixed
     */
    public function allAssessmentDocsWithOwner();

    /**
     * Deletes a Project.
     *
     * @param int
     */
    public function delete($projectId);

    /**
     * Updates a Project.
     *
     * @param int
     * @param array
     */
    public function update($projectId, array $data);

    /**
     * Create a Project.
     *
     * @param array
     */
    public function create(array $data);

    /**
     * Return Project for the given id
     *
     * @param $projectId
     * @return mixed
     */
    public function findOrFail($projectId);
}
