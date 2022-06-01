<?php

namespace App\Services;

use App\Enums\MediaStatus;
use App\Enums\MediaType;
use App\Exceptions\EvoliException;
use App\Facades\Evoli;
use App\Models\Media;
use App\Repositories\MediaRepositoryInterface;
use Auth;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    private $mediaRepository;

    public function __construct(MediaRepositoryInterface $repository)
    {
        $this->mediaRepository = $repository;
    }

	/**
	 * @param UploadedFile $file
	 * @param string $caption
	 * @return Media
	 * @throws EvoliException This exception is thrown when the response of Evoli cannot be handled.
	 */
    public function storeFile(UploadedFile $file, $attributes = [])
    {
        $media = $this->mediaRepository->create([], Auth::id());
        $type = $attributes['type'];
        $caption = $attributes['caption'];

        // empty type causeses empty contents error on http request ( also 0 passed as string ) so just dont pass it in this case
        if(empty($type)) {
            unset($attributes['type']);
        }

        //store file locally - for the moment
        $uploadPath = $file->storeAs(
            'uploads', $media->id
        );
        if ($uploadPath === false) {
            Log::error('File could not be uploaded. Local path not found.');
            $media->status = MediaStatus::FAILED_UPLOAD;
            $media->save();
            abort(500, 'File could not be uploaded. Local path not found.');
        }

        try {
            $data = Evoli::transferMedia($file, $attributes);
        } catch (EvoliException $exception) {
            Log::error($exception);
            $media->status = MediaStatus::FAILED_CONVERT;
            $media->save();
            Storage::delete($uploadPath);

            if($exception->getCode() === 415) {
                abort(415, 'Unsupported Media Type');
            }

            abort(503, $exception->getMessage());
        }



        try {
        	$media->streamingId = $data['id'];
	        $media->streamingURL_720p = ($type === MediaType::THREE_SIXTY) ? $data['convertedURL'] : $data['convertedURL_720p'];
	        $media->streamingURL_1080p = $data['convertedURL_1080p'];
	        $media->streamingURL_2160p = $data['convertedURL_2160p'];
	        $media->status = MediaStatus::PENDING;
	        $media->fileName = $file->getClientOriginalName();
	        $media->caption = $caption;
	        $media->type = $type;
	        $media->previewURL = $data['previewURL'];
        } catch (Exception $exception) {
	        throw new EvoliException("Attribute not found in Evoli Response.", 400, $exception->getPrevious());
        }

        $media->save();

        $info = Evoli::getMediaInformation($media->streamingId);
        \Log::info('media info ', $info);

        return $media;
    }

    /**
     * Check the remote status of an media upload
     *
     * @param  string $streamingId The media to get the status from
     * @throws EvoliException
     * @return int
     */
    public function fetchStatusFromEvoli(string $streamingId)
    {
        $response = Evoli::getMediaInformation($streamingId);
        $statusString = $response['status'];
        return MediaStatus::getStatusByString($statusString);
    }

    /**
     * Function updates the status of a media by fetching the status from evoli
     *
     * @param  Media  $media The media to collect
     */
    public function updateStatus(Media $media)
    {
        if ($media->streamingId === null) {
            return;
        }
        try {
            $status = $this->fetchStatusFromEvoli($media->streamingId);
            if ($status !== null) {
                $media->status = $status;
                $this->mediaRepository->save($media);
            }
        } catch (EvoliException $exception) {
            Log::error('Can not update media status.', ['exception' => $exception]);
        }
    }

    /**
     * Helper class to update all media by their status
     *
     * @param  int  $status The status of the media that should be updated
     * @return array The updated media
     */
    public function updateMediaStatusByCurrentStatus(int $status)
    {
        $selectedMedia = $this->mediaRepository->getMediaByStatus($status);
        foreach ($selectedMedia as $media) {
            $this->updateStatus($media);
        }
        return $selectedMedia;
    }
}
