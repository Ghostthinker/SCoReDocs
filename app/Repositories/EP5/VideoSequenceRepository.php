<?php

namespace App\Repositories\EP5;

use App\Models\EP5\VideoSequence;
use App\User;
use Throwable;

class VideoSequenceRepository implements VideoSequenceRepositoryInterface
{
    /**
     * @param $media_id
     * @return mixed
     */
    public function getByMediaId($media_id)
    {
        return VideoSequence::byMediaId($media_id)->get();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return VideoSequence::all();
    }

    /**
     * @param $videoSequence_id
     * @return mixed
     */
    public function get($videoSequence_id)
    {
        return VideoSequence::find($videoSequence_id);
    }

    /**
     * @param $videoSequence_id
     * @return mixed
     */
    public function getWithUser($videoSequence_id)
    {
        return VideoSequence::where('id', $videoSequence_id)->with('user')->first();
    }

    /**
     * @param array     $data
     * @param User|null $user
     *
     * @return mixed
     */
    public function create(array $data, $user = null)
    {
        $videoSequence = VideoSequence::make($data);
        if ($user !== null) {
            $videoSequence->user()->associate($user);
        }
        if ($videoSequence->save()) {
            return $videoSequence;
        }
        return false;
    }

    /**
     * @param $videoSequence_id
     * @param  array  $data
     * @return bool
     */
    public function update($videoSequence_id, array $data)
    {
        try {
            $videoSequence = VideoSequence::findOrFail($videoSequence_id);
        } catch (Throwable $exception) {
            return false;
        }

        try {
            $videoSequence->update($data);
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param $videoSequence_id
     *
     * @return bool
     */
    public function delete($videoSequence_id)
    {
        return VideoSequence::destroy($videoSequence_id);
    }
}
