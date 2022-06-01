<?php

namespace App\Http\Controllers;

use App\Events\DeleteNewsEvent;
use App\Events\NewNewsEvent;
use App\Events\UpdateNewsEvent;
use App\Http\Resources\NewsResource;
use App\Mail\NewNews;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use App\Rules\PermissionSet;
use App\Services\Xapi\XapiNewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NewsController extends Controller
{

    public function index(Request $request, NewsRepositoryInterface $repository)
    {
        $user = Auth::user();
        $audits = $repository->all();
        $collection = NewsResource::collection($audits);
        $response['news'] = $collection;
        $response['can_edit_news'] = $user->can(PermissionSet::EDIT_NEWS);
        $response['can_create_news'] = $user->can(PermissionSet::CREATE_NEWS);
        return $response;
    }

    public function store(
        Request $request,
        NewsRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        $news = $repository->create($request->all());
        if ($news) {
            $users = $userRepository->all();
            foreach($users as $user){
                if($user->id === Auth::user()->id) continue;
                $mailData['name'] = $user->name;
                $mailData['title'] = $news->title;
                $mailData['content'] = $news->shorterContent;
                Mail::to($user->email)->send(new NewNews($mailData));
            }
            broadcast(new NewNewsEvent(NewsResource::make($news)));
            XapiNewsService::createNews($request->fullUrl(), $news);
        }
        return $news;
    }

    public function update(
        Request $request,
        NewsRepositoryInterface $repository
    ) {
        $news = $repository->get($request->get('id'));
        $success = $repository->update($news->id, $request->all());
        if ($success && $news) {
            $news = $repository->get($news->id);
            $news->usersRead()->detach();
            broadcast(new UpdateNewsEvent(NewsResource::make($news)));
            XapiNewsService::updateNews($request->fullUrl(), $news);
            return response($news, 200);
        }
        abort(404, 'News not found');
    }

    public function read(
        Request $request,
        $news_id,
        NewsRepositoryInterface $repository
    ) {
        $news = $repository->get($news_id);
        if ($news) {
            $user = Auth::user();
            $news->usersRead()->attach($user->id);
            XapiNewsService::readNews($request->fullUrl(), $news);
            response('ok', 200);
        }
        return false;
    }

    public function delete(
        Request $request,
        $news_id,
        NewsRepositoryInterface $repository
    ) {
        $news = $repository->get($news_id);
        $success = $repository->delete($news->id);
        if (!$success || !$news) {
            abort(404, 'News could not be deleted');
        }
        XapiNewsService::deleteNews($request->fullUrl(), $news);
        broadcast(new DeleteNewsEvent($news->id));
        return $success;
    }
}
