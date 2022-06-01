<?php

namespace App\Http\Controllers;

use App\Enums\MediaType;
use App\Exceptions\EvoliException;
use App\Models\Media;
use App\Repositories\MediaRepositoryInterface;
use App\Services\EvoliService;
use App\Services\UploadService;
use App\Services\Xapi\XapiMediaService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param MediaRepositoryInterface $repository
     *
     * @return Media[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(MediaRepositoryInterface $repository)
    {
        return $repository->getAll();
    }

    /**
     * Returns the media by id
     *
     * @param  Request  $request
     * @param  int  $media_id
     * @param  MediaRepositoryInterface  $repository
     * @return mixed
     */
    public function getByMediaId(Request $request, int $media_id, MediaRepositoryInterface $repository)
    {
        return $repository->get($media_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  UploadService  $uploadService

     */
    public function store(Request $request, UploadService $uploadService)
    {
        $path = $request->file('upload')->getRealPath();
        $mimeType = File::mimeType($path);
        if(!str_contains($mimeType, 'video')) {
            return response('Unsupported Media Type', 415);
        }

        $request->validate([
            'upload' => 'bail|required|max:4000000|min:200|', // 4GB limit
        ],
        [
            'upload.min' => 'Die Videodatei ist zu klein, sie muss mindestens 200kB groß sein.',
            'upload.max' => 'Die Videodatei ist zu groß, sie darf maximal 4GB groß sein.',
            'upload.required' => 'Es muss ein Video zum hochladen ausgewählt werden.'
        ]);

        $file = $request->file('upload');
        $caption = $request->input('caption');
        $type = $request->input('mediaType360') == "true" ? MediaType::THREE_SIXTY : MediaType::DEFAULT;
        $projectId = $request->input('projectId');
        $options = [];
        if($type == MediaType::THREE_SIXTY) {
            $options += [
                'camX' => 0,
                'camY' => 0,
                'camZ' => 0
            ];
        }
        $attributes = [
            'type' => $type,
            'caption' => $caption,
            'options' => $options
        ];


        $media = $uploadService->storeFile($file, $attributes);
        $data = $request->all();
        $data['id'] = $request->fullUrl() . '/create';
        $data['title'] = $caption;
        $data['type'] = $type;
        $data['currentTime'] = 0;
        XapiMediaService::create(null, $media, $data, $projectId);

        return $media;
    }

    /**
     * Display the specified resource.
     *
     * @param                          $id
     * @param MediaRepositoryInterface $repository
     *
     * @return Media|Media[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function show($id, MediaRepositoryInterface $repository)
    {
        return $repository->findOrFail($id);
    }

    /**
     * @param $media_id
     * @param  MediaRepositoryInterface  $repository
     * @param  EvoliService  $evoliService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download($media_id, MediaRepositoryInterface $repository, EvoliService $evoliService)
    {
        $media = $repository->findOrFail($media_id);
        $url = $evoliService->getEndpoint('download/'.$media->streamingId);
        return Redirect::away($url);
    }

    /**
     * @param $media_id
     * @param  MediaRepositoryInterface  $repository
     * @param  EvoliService  $evoliService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadUncompressed($media_id, MediaRepositoryInterface $repository, EvoliService $evoliService)
    {
        $media = $repository->findOrFail($media_id);
        $url = $evoliService->getEndpoint('download/'.$media->streamingId.'/uncompressed');
        return Redirect::away($url);
    }
}
