<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataExportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'statement_count' => $this->statement_count,
            'downloaded_count' => $this->downloaded_count,
            'filesize' => $this->humanFileSize($this->filesize),
            'created_at' => $this->created_at->format(config('app')['date_format']),
            'updated_at' => $this->updated_at->format(config('app')['date_format']),
        ];
    }

    private function humanFileSize($size)
    {
        if ($size >= 1073741824) {
            $fileSize = round($size / 1024 / 1024 / 1024,1) . 'GB';
        } elseif ($size >= 1048576) {
            $fileSize = round($size / 1024 / 1024,1) . 'MB';
        } elseif($size >= 1024) {
            $fileSize = round($size / 1024,1) . 'KB';
        } else {
            $fileSize = $size . ' bytes';
        }
        return $fileSize;
    }
}
