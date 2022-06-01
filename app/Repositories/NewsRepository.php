<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository implements NewsRepositoryInterface
{

    public function get($id)
    {
        return News::find($id);
    }

    public function all()
    {
        return News::all();
    }

    public function delete($id): int
    {
        News::where('id', $id)->firstOrFail()->usersRead()->detach();
        return News::where('id', $id)->delete();
    }

    public function update($id, array $data): bool
    {
        $news = News::findOrFail($id);
        return $news->update($data);
    }

    public function create(array $data)
    {
        $news = News::create($data);
        if($news->save()) {
            return $news;
        }
        return null;
    }
}
