<?php

namespace App\Repositories\EP5;

use App\Models\EP5\PlaybackCommand;
use App\User;
use Throwable;

class PlaybackCommandRepository implements PlaybackCommandRepositoryInterface
{
    public function get($instructionId)
    {
        return PlaybackCommand::find($instructionId);
    }

    public function all()
    {
        return PlaybackCommand::all();
    }

    public function delete($playbackId)
    {
        PlaybackCommand::destroy($playbackId);
    }

    public function update($playbackId, array $data)
    {
        try {
            $playbackcommand = PlaybackCommand::findOrFail($playbackId);
        } catch (Throwable $exception) {
            return false;
        }

        try {
            $playbackcommand->update($data);
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param array     $data
     * @param User|null $user
     *
     * @return PlaybackCommand|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data, $user = null)
    {
        $playbackCommand = PlaybackCommand::make($data);
        if ($user !== null) {
            // $playbackCommand->user_id = $user->id;
            $playbackCommand->user()->associate($user);
        }
        if ($playbackCommand->save()) {
            return $playbackCommand;
        }
    }

    public function getByMediaId($mediaId)
    {
        return PlaybackCommand::byMediaId($mediaId)->get();
    }
}
