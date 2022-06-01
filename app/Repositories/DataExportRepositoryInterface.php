<?php

namespace App\Repositories;

use App\Models\DataExport;
use Symfony\Component\VarDumper\Cloner\Data;

interface DataExportRepositoryInterface
{
    /**
     * Get's a Object by it's ID
     *
     * @param  int
     */
    public function get($id);

    /**
     * Get's a Object by it's ID
     *
     * @param  DataExport
     */
    public function save($dataExport);

    /**
     * Get's the latest Object.
     *
     * @return DataExport
     */
    public function getLatest();

    /**
     * @param array $data
     *
     * @param null $user
     * @return mixed
     */
    public function create(array $data, $user = null);

    /**
     * @param  string  $column
     * @param  array  $data
     * @return mixed
     */
    public function whereNotIn(string $column, array $data);
}
