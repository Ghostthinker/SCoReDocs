<?php

namespace App\Repositories;

use App\Models\Link;

class LinkRepository implements LinkRepositoryInterface, EloquentRepositoryInterface
{
    public function get($id)
    {
        return Link::find($id);
    }

    public function all()
    {
        return Link::all();
    }

    public function create(array $data)
    {
        $link = Link::make($data);
        if ($link->save()) {
            return $link;
        }
        return null;
    }

    public function getByRefId($id)
    {
        return Link::where('ref_id', $id)->get();
    }

    public function delete($id)
    {
        Link::destroy($id);
    }

    /**
     * @param  Link  $object  The link to update
     * @param  array  $data
     *
     * @return bool|void Indicator if update is successful
     */
    public function update($object, array $data)
    {
        Link::findOrFail($object->id)->update($data);
    }

    /**
     * @param $id
     *
     * @return Link[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBySection($id)
    {
        return Link::where('section_id', $id)->get();
    }

    /**
     * @param array $input
     *
     * @return int
     */
    public function deleteIds(array $input)
    {
        return Link::whereIn('id', array_values($input))->delete();
    }

    /**
     * @param array $values
     *
     * @return int
     */
    public function insertRecordAndGetId(array $values)
    {
        return Link::insertGetId($values);
    }
}
