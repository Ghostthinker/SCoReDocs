<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Repositories\FileRepositoryInterface;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param FileRepositoryInterface $fileRepository
     *
     * @return File[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(FileRepositoryInterface $fileRepository)
    {
        return $fileRepository->all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param FileService $fileService
     *
     * @return mixed
     */
    public function store(Request $request, FileService $fileService)
    {
        if (!$request->hasFile('upload')) {
            throw new HttpException(422);
        }

        $uploadFile = $request->file('upload');
        $file_response = $fileService->storeFile($uploadFile);
        $url = $file_response['url'];
        \Log::info('file url of upload: ' . $url);

        // handle response required by CKEDITOR upload plugin
        $type = $_GET['type'] ?? null;
        if ($type === 'ckeditor_score_image') {
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $response = "<script>window.parent.CKEDITOR.tools.callFunction(${CKEditorFuncNum}, '${url}');</script>";

            @header('Content-type: text/html; charset=utf-8');
            return $response;
        }
        return response()->json($file_response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $fileID
     * @param FileRepositoryInterface $fileRepository
     *
     * @return Response
     */
    public function show($fileID, FileRepositoryInterface $fileRepository)
    {
        return $fileRepository->get($fileID);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $fileID
     *
     * @return void
     */
    public function edit($fileID)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $fileID
     * @param FileRepositoryInterface $fileRepository
     *
     * @return Response
     */
    public function update(Request $request, $fileID, FileRepositoryInterface $fileRepository)
    {
        if (!is_numeric($fileID)) {
            $file = $fileRepository->getByName($fileID);
            if ($file) {
                $fileID = $file->id;
            }
        }
        return $fileRepository->update($fileID, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $fileID
     * @param FileRepositoryInterface $fileRepository
     *
     * @return Response
     */
    public function destroy(Request $request, $fileID, FileRepositoryInterface $fileRepository)
    {
        return $fileRepository->delete($fileID);
    }

    /**
     * @param $filename
     *
     * @return BinaryFileResponse|void
     */
    public function deliver($filename)
    {
        $filepath = Storage::disk('uploads')->path($filename);
        return response()->file($filepath);
    }
}
