<?php

namespace App\Http\Controllers;

use App\Http\Resources\DataExportResource;
use App\Repositories\DataExportRepositoryInterface;
use Illuminate\Support\Facades\Redirect;
use Storage;

class DataExportController extends Controller
{
    public function download(DataExportRepositoryInterface $repository)
    {
        $latestExport = $repository->getLatest()->firstOrFail();
        $latestExport->downloaded_count++;
        $repository->save($latestExport);
        return Storage::disk('data_export')->download($latestExport->filename, null);
    }

    public function lastDataExport(DataExportRepositoryInterface $repository) {
        return DataExportResource::make($repository->getLatest()->first());
    }
}
