<?php

namespace App\Repositories;

use App\Models\Profile;
use Throwable;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * @param $id
     *
     * @return Profile|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByUser($id)
    {
        return Profile::where('user_id', $id)->first();
    }

    /**
     * @return mixed
     */
    public function getProfiles() {
        return Profile::all();
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return bool
     */
    public function update($id, array $data)
    {
        try {
            return Profile::findOrFail($id)->update($data);
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * Param $id is the user id
     *
     * @param $id
     * @return Profile|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function get($id)
    {
        return Profile::where('user_id', $id)->first();
    }
}
