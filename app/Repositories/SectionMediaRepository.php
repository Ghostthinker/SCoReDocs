<?php

namespace App\Repositories;

use App\Models\SectionMedia;

class SectionMediaRepository implements SectionMediaRepositoryInterface
{
    public function getBySection($id)
    {
        return SectionMedia::where('section_id', $id)->get();
    }

    public function create(array $data)
    {
        $model = SectionMedia::make($data);
        if ($model->save()) {
            return $model;
        }
        return null;
    }

    public function deleteByRef(array $input)
    {
        return SectionMedia::whereIn('ref_id', array_values($input))->delete();
    }
}
