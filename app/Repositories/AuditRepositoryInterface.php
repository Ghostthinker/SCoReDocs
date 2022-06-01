<?php

namespace App\Repositories;

interface AuditRepositoryInterface
{
    /**
     * @param $sectionId
     *
     * @return mixed
     */
    public function getCountBy($sectionId);
}
