<?php

namespace App\Repositories;

use App\Models\Media;

class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @param $id
     *
     * @return Media|Media[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function get($id)
    {
        return Media::find($id);
    }

    /**
     * @param $id
     * @return Media|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getWithUser($id)
    {
        return Media::where('id', $id)->with('user')->first();
    }

    /**
     * @param $media
     * @return mixed
     */
    public function save($media)
    {
        return $media->save();
    }

    /**
     * @return Media[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getAll()
    {
        return Media::all();
    }

    /**
     * @param $id
     *
     * @return Media|Media[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function findOrFail($id)
    {
        return Media::findOrFail($id);
    }

    /**
     * @param  array  $data
     *
     * @param  null  $user
     * @return Media|\Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data, $user = null)
    {
        $model = Media::make($data);
        if ($user !== null) {
            $model->user()->associate($user);
        }
        if ($model->save()) {
            return $model;
        }
        return null;
    }

    public function getMediaByStatus($status)
    {
        // convert to array if single value
        if (!is_array($status)) {
            $status = [$status];
        }
        return Media::whereIn('status', $status)->get();
    }
}
