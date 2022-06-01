<?php

namespace App\Repositories;

use App\Models\SectionMedia;

interface SectionMediaRepositoryInterface
{
    /**
     * @param $id
     *
     * @return mixed
     */
    public function getBySection($id);

    /**
     * @param array $data
     *
     * @return SectionMedia|\Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data);

    /**
     * @param array $input
     *
     * @return int
     */
    public function deleteByRef(array $input);
}
