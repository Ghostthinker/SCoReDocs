<?php

namespace App\Http\Controllers\EP5;

use App\Exceptions\EvoliException;
use App\Facades\Evoli;
use App\Http\Controllers\Controller;
use App\Http\Requests\VideoSequenceRequest;
use App\Http\Resources\EP5\VideoSequenceResource;
use App\Models\EP5\VideoSequence;
use App\Repositories\EP5\VideoSequenceRepositoryInterface;
use App\Repositories\MediaRepositoryInterface;
use App\Services\EvoliService;
use App\Services\Xapi\XapiVideoSequenceService;
use Auth;
use Illuminate\Support\Facades\Redirect;

class VideoSequenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $media_id
     * @param  VideoSequenceRepositoryInterface  $videoSequenceRepository
     * @return VideoSequence
     */
    public function index($media_id, VideoSequenceRepositoryInterface $videoSequenceRepository)
    {
        return $videoSequenceRepository->getByMediaId($media_id);
    }

    /**
     * @param $media_id
     * @param $sequence_id
     * @param  VideoSequenceRepositoryInterface  $videoSequenceRepository
     * @return mixed
     */
    public function show($media_id, $sequence_id, VideoSequenceRepositoryInterface $videoSequenceRepository)
    {
        return $videoSequenceRepository->get($sequence_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VideoSequenceRequest  $request
     * @param  VideoSequenceRepositoryInterface  $videoSequenceRepository
     * @param  MediaRepositoryInterface  $mediaRepository
     * @return VideoSequenceResource
     */
    public function store(VideoSequenceRequest $request, VideoSequenceRepositoryInterface $videoSequenceRepository, MediaRepositoryInterface $mediaRepository)
    {
        $data = $request->getParsedData();
        $user = Auth::user();

        $sequence = $videoSequenceRepository->create($data, $user);
        $media = $mediaRepository->get($sequence->video_nid);
        XapiVideoSequenceService::create($media, $sequence, $request->fullUrl());
        return $sequence;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  VideoSequenceRequest  $request
     * @param  string  $media_id
     * @param  string  $sequences_id
     * @param  VideoSequenceRepositoryInterface  $videoSequenceRepository
     * @param  MediaRepositoryInterface  $repository
     * @return \Illuminate\Http\Response
     */
    public function update(
        VideoSequenceRequest $request,
        string $media_id,
        string $sequences_id,
        VideoSequenceRepositoryInterface $videoSequenceRepository,
        MediaRepositoryInterface $repository
    ) {
        $data = $request->getParsedData();
        $success = $videoSequenceRepository->update($sequences_id, $data);

        $videoSequence = $videoSequenceRepository->get($sequences_id);
        $media = $repository->get($videoSequence->video_nid);
        XapiVideoSequenceService::update($media, $videoSequence, $request->fullUrl());

        return $success;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $videoSequence_id
     * @param  VideoSequenceRepositoryInterface  $videoSequenceRepository
     * @return bool
     */
    public function destroy(
        $videoSequence_id,
        VideoSequenceRepositoryInterface $videoSequenceRepository
    ) {
        $videoSequenceRepository->get($videoSequence_id);
        return $videoSequenceRepository->delete($videoSequence_id);
    }


    public function download(
        string $media_id,
        string $sequences_id,
        VideoSequenceRepositoryInterface $videoSequenceRepository,
        MediaRepositoryInterface $repository,
        EvoliService $evoliService
    ){
        $media = $repository->findOrFail($media_id);
        $sequence = $videoSequenceRepository->get($sequences_id);

        if($sequence->camera_yaw !== null && $sequence->camera_pitch !== null) {

            try {
                $url = $evoliService->getEndpoint('download/' . $media->streamingId . '/360clip' . '/?start=' .
                    $sequence->timestamp / 1000 . '&end=' . ($sequence->timestamp / 1000 + $sequence->duration / 1000) .
                    '&yaw=' . $sequence->camera_yaw .'&pitch=' . $sequence->camera_pitch);
                $result = $evoliService->create360VideoSequence($url);

                return $response = array(
                    'url' => $url,
                    'status' => $result
                );
            } catch (EvoliException $e) {
                return $e;
            }
        } else {
            $url = $evoliService->getEndPoint('download/' . $media->streamingId . '/?start=' . $sequence->timestamp/1000 .'&end='. ($sequence->timestamp/1000 + $sequence->duration/1000));
            return $response = array(
                'url' => $url,
                'status' => 200
            );
        }
    }

    public function progress(
        string $media_id,
        string $sequences_id,
        VideoSequenceRepositoryInterface $videoSequenceRepository,
        MediaRepositoryInterface $repository,
        EvoliService $evoliService
    ){
        $media = $repository->findOrFail($media_id);
        $sequence = $videoSequenceRepository->get($sequences_id);
        $url = $evoliService->getEndpoint('download/' . $media->streamingId . '/progress' . '/?start=' .
            $sequence->timestamp / 1000 . '&end=' . ($sequence->timestamp / 1000 + $sequence->duration / 1000) .
            '&yaw=' . $sequence->camera_yaw .'&pitch=' . $sequence->camera_pitch);
        return $evoliService->getConversionProgress($url);
    }

}
