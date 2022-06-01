<?php

namespace App\Repositories;

interface ProfileRepositoryInterface
{
    public function getByUser($id);
    public function update($id, array $data);
}
