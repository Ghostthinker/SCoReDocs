<?php

namespace App\Repositories;

use App\Models\Section;

class SectionTrashRepository implements SectionTrashRepositoryInterface
{
    public function get($id)
    {
        return Section::onlyTrashed()->findOrFail($id);
    }

    public function getBy($id, $offset = 0, $limit = -1)
    {
        $where = Section::onlyTrashed()->where('project_id', $id)
            ->with(
                [
                    'audits' => function ($query) {
                        $query->where('event', 'deleted')->latest();
                    },
                ]
            );
        if ($limit == -1) {
            $limit = $where->count();
        }
        return $where->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
    }

    public function getCountBy($id)
    {
        $where = Section::onlyTrashed()->where('project_id', $id)
            ->with(
                [
                    'audits' => function ($query) {
                        $query->where('event', 'deleted')->latest();
                    },
                ]
            );
        return $where->count();
    }
}
