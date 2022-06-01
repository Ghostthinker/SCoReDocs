<?php

namespace App\Repositories;

use App\Models\Section;
use OwenIt\Auditing\Models\Audit;
use Throwable;

class AuditRepository implements AuditRepositoryInterface
{
    /**
     * @param     $sectionId
     * @param int $offset
     * @param int $limit
     *
     * @return mixed
     */
    public function getBy($sectionId, $offset = 0, $limit = -1)
    {
        $where = Audit::where('auditable_id', '=', $sectionId)->where('auditable_type', '=', Section::class)->where('new_values', '!=', '[]');
        if ($limit == -1) {
            $limit = $where->count();
        }
        return $where->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
    }

    /**
     * @param $sectionId
     *
     * @return mixed
     */
    public function getCountBy($sectionId)
    {
        $where = Audit::where('auditable_id', '=', $sectionId)->where('auditable_type', '=', Section::class)->where('new_values', '!=', '[]');
        return $where->count();
    }

    public function get($id)
    {
        return Audit::find($id);
    }

    public function getAll()
    {
        return Audit::all();
    }

    public function delete($id)
    {
        $count = Audit::destroy($id);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function update($id, array $data)
    {
        try {
            return Audit::findOrFail($id)->update($data);
        } catch (Throwable $exception) {
            return false;
        }
    }

    public function create(array $data)
    {
        $section = Audit::make($data);
        if ($section->save()) {
            return $section;
        }
    }

    public function findOrFail($id)
    {
        return Audit::findOrFail($id);
    }
}
