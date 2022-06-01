<?php

namespace App\Repositories;

use App\Models\File;
use Throwable;

class FileRepository implements FileRepositoryInterface, EloquentRepositoryInterface
{

    /**
     * @param $filetId
     *
     * @return mixed
     */
    public function get($fileId)
    {
        if (is_numeric($fileId)) {
            return File::find($fileId);
        }
        // fallback to file name
        return File::firstWhere('filename', $fileId);
    }

    /**
     * @param $fileId
     *
     * @return File|\Illuminate\Database\Eloquent\Model
     */
    public function getByName($fileId)
    {
        return File::firstWhere('filename', $fileId);
    }

    /**
     * @param $filePath
     *
     * @return File|\Illuminate\Database\Eloquent\Model
     */
    public function getByPath($filePath)
    {
        return File::firstWhere('path', $filePath);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function all()
    {
        return File::all();
    }

    /**
     * @param $filetId
     */
    public function delete($filetId)
    {
        File::destroy($filetId);
    }

    /**
     * @param $filetId
     * @param  array  $data
     *
     * @return bool
     */
    public function update($filetId, array $data)
    {
        try {
            $file = File::findOrFail($filetId);
        } catch (Throwable $exception) {
            return false;
        }

        try {
            $file->update($data);
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        $file = File::make($data);

        if ($file->save()) {
            return $file;
        }
        return null;
    }
}
