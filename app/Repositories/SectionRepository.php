<?php

namespace App\Repositories;

use App\Models\Section;
use Auth;
use Throwable;

class SectionRepository implements SectionRepositoryInterface, EloquentRepositoryInterface
{
    /**
     * @param int $sectionId
     *
     * @return Section|array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function get($sectionId)
    {
        return Section::find($sectionId);
    }

    /**
     * @param $sectionId
     *
     * @return Section|Section[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($sectionId)
    {
        return Section::findOrFail($sectionId);
    }

    /**
     * @param int $sectionId
     *
     * @return Section|array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function withTrashed($sectionId)
    {
        return Section::withTrashed()->findOrFail($sectionId);
    }

    /**
     * @param $projectId
     *
     * @return Section[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getAllByProjectId($projectId)
    {
        return Section::where('project_id', $projectId)->orderBy('index', 'asc')->get();
    }

    /**
     * @param $projectId
     * @return Section[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|mixed
     */
    public function getAllByProjectIdWithProjectAndUserCollapse($projectId)
    {
        return Section::where('project_id', $projectId)->with(['project', 'user_collapse' => function($q) { $q->where
            ('user_id', Auth::id()); }])->orderBy('index', 'asc')->get();
    }

    /**
     * @param $projectId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllByProjectIdWithProjectPaginate($projectId)
    {
        return Section::where('project_id', $projectId)->with(['project', 'user_collapse' => function($q) { $q->where
        ('user_id', Auth::id()); }])->orderBy('index', 'asc')->paginate(25);
    }

    /**
     * @return Section[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function all()
    {
        return Section::all();
    }

    /**
     * @param $sectionId
     *
     * @return bool
     */
    public function delete($sectionId)
    {
        $count = Section::destroy($sectionId);
        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * Updates a section
     *
     * @param  Section  $section The section model to update
     * @param  array  $data The new data as array
     *
     * @return bool The indicator if operation was successful
     */
    public function update($section, array $data)
    {
        try {
            $section->update($data);
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param array $data
     *
     * @return Section|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $section = Section::make($data);
        if ($section->save()) {
            return $section;
        }
        return null;
    }

    /**
     * @param $sectionId
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\MorphMany|object|null
     */
    public function getLastAudit($sectionId)
    {
        return Section::find($sectionId)->audits()->latest()->first();
    }
}
