<?php

use App\Services\FileService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

class CompressUploadedImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $files = \App\Models\File::all();
        foreach($files as $file) {
            $relativePath = 'uploads/' . $file->filename;
            $newPath = substr_replace($relativePath, '_original', strpos($relativePath, '.'), 0);
            try {
                Storage::copy($relativePath, $newPath);
            } catch (FileExistsException | FileNotFoundException $exception) {
                continue;
            }

            FileService::compressFile($relativePath);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
