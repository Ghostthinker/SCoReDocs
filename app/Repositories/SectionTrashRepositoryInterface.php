<?php

namespace App\Repositories;

interface SectionTrashRepositoryInterface
{
    public function get($id);
    public function getBy($id, $offset = 0, $limit = -1);
    public function getCountBy($id);
}
