<?php

namespace App\Repositories;

use App\Models\DataExport;

class DataExportRepository implements DataExportRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return DataExport::findOrFail($id);
    }

    /**
     * @param $dataExport
     * @return mixed
     */
    public function save($dataExport)
    {
        return $dataExport->save();
    }

    /**
     * @param  array  $data
     * @param  null  $user
     * @return mixed|null
     */
    public function create(array $data, $user = null)
    {
        $model = DataExport::make($data);
        if ($model->save()) {
            return $model;
        }
        return null;
    }

    /**
     * @return DataExport
     */
    public function getLatest() {
        return DataExport::latest();
    }

    public function delete($id)
    {
        $count = DataExport::destroy($id);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function all()
    {
        return DataExport::all();
    }

    public function whereNotIn(string $column, array $data)
    {
        return DataExport::whereNotIn($column, $data)->get();
    }
}
