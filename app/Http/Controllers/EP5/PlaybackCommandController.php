<?php

namespace App\Http\Controllers\EP5;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaybackCommandRequest;
use App\Http\Resources\EP5\PlaybackCommandResource;
use App\Repositories\EP5\PlaybackCommandRepositoryInterface;
use App\Services\Xapi\XapiPlaybackCommandService;
use Auth;

class PlaybackCommandController extends Controller
{
    public function index($media_id, PlaybackCommandRepositoryInterface $playbackCommandRepository)
    {
        $playbackCommands = $playbackCommandRepository->getByMediaId($media_id);

        return PlaybackCommandResource::collection($playbackCommands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PlaybackCommandRequest  $request
     * @param  \App\Repositories\EP5\PlaybackCommandRepositoryInterface  $playbackCommandRepository
     *
     * @return PlaybackCommandResource
     */
    public function store(
        PlaybackCommandRequest $request,
        PlaybackCommandRepositoryInterface $playbackCommandRepository
    ) {
        $data = $request->getParsedData();
        $user = Auth::user();

        $playbackCommand = $playbackCommandRepository->create($data, $user);
        XapiPlaybackCommandService::createPlaybackCommand($playbackCommand, $data, $request->fullUrl());

        return new PlaybackCommandResource($playbackCommand);
    }

    public function destroy(
        $mediaId,
        $playbackCommand_id,
        PlaybackCommandRequest $request,
        PlaybackCommandRepositoryInterface $playbackCommandRepository
    ) {
        $playbackCommand = $playbackCommandRepository->get($playbackCommand_id);
        XapiPlaybackCommandService::deletePlaybackCommand($playbackCommand, $request->fullUrl());
        return $playbackCommandRepository->delete($playbackCommand_id);
    }

    public function update(
        $mediaId,
        $playbackId,
        PlaybackCommandRequest $request,
        PlaybackCommandRepositoryInterface $playbackCommandRepository
    ) {
        $data = $request->getParsedData();
        $playbackCommand = $playbackCommandRepository->get($playbackId);
        XapiPlaybackCommandService::updatePlaybackCommand($playbackCommand, $data, $request->fullUrl());
        return $playbackCommandRepository->update($playbackId, $data);
    }
}
