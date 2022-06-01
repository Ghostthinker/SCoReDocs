<?php

namespace App\Repositories;

use App\Models\LockTimeout;

class TimeoutRepository implements TimeoutRepositoryInterface, EloquentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return LockTimeout::get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return LockTimeout::all();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return LockTimeout::destroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function update($object, array $data)
    {
        return LockTimeout::findOrFail($object->id)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return LockTimeout::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getBySectionId(int $sectionId)
    {
        return LockTimeout::where('section_id', $sectionId)->get()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimeout(int $sectionId)
    {
        return $this->getBySectionId($sectionId)->touch();
    }

    /**
     * {@inheritdoc}
     */
    public function createBySectionId(int $sectionId)
    {
        $data = ['section_id' => $sectionId];
        return $this->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBySectionId(int $sectionId)
    {
        return LockTimeout::where('section_id', $sectionId)->delete();
    }
}
