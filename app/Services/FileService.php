<?php

namespace App\Services;

use App\Repositories\FileRepositoryInterface;
use Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * Class FileService
 *
 * @package App\Services
 */
class FileService
{
    private $fileRepository;

    public function __construct(FileRepositoryInterface $repository)
    {
        $this->fileRepository = $repository;
    }

    /**
     * @param UploadedFile $file
     *
     * @return array
     */
    public function storeFile(UploadedFile $file)
    {
        $filepath = $file->store('uploads');
        $newPath = substr_replace($filepath, '_original', strpos($filepath, '.'), 0);

        Storage::copy($filepath, $newPath);
        FileService::compressFile($filepath);

        $caption = $file->getClientOriginalName();
        $filename = basename($filepath);
        $url = route('file.deliver', $filename, false);

        $metadata = '';
        $store_path = storage_path('app/uploads') . DIRECTORY_SEPARATOR . $filename;

        // get metadata, if available
        $data = Image::make($store_path)->exif();
        if ($data) {
            $metadata = json_encode($metadata);
        }

        //create file entity
        $file = $this->fileRepository->create([
            'filename' => $filename,
            'uid' => Auth::id(),
            'storage' => 'uploads',
            'path' => $url,
            'caption' => $caption,
            'filesize' => $file->getSize(),
            'status' => 1,
            'meta' => $metadata,
        ]);

        return [
            'uploaded' => 1,
            'fileName' => $filename,
            'caption' => $caption,
            'url' => $url,
            'fileID' => $file->id,
            'metadata' => $metadata,
        ];
    }

    /**
     * Compress an image for a given filepath
     *
     * @param $filepath
     */
    public static function compressFile($filepath) {
        $image = Image::make(storage_path('app/' . $filepath));

        if ($image->width() > 1000) {
            $image->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        if ($image->height() > 1000) {
            $image->resize(null, 1000, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $image->save();

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize(storage_path('app/' . $filepath));
    }
}
