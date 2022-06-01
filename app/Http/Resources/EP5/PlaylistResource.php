<?php

namespace App\Http\Resources\EP5;

use App\Enums\MediaStatus;
use App\Models\EP5\VideoSequence;
use App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
{
    protected $sectionTitle;

    public function sectionTitle($value)
    {
        $this->sectionTitle = $value;
        return $this;
    }

    public function toArray($request)
    {
        $playListArray = [
            'title' => $this->sectionTitle,
            'media' => []
        ];
        foreach ($this->resource as $playListItem) {
            if ($playListItem instanceof VideoSequence) {
                $videoSequenceResource = VideoSequencePlayListResource::make($playListItem);
                if($playListItem->media->status != MediaStatus::CONVERTED){
                    $playListArray['isPending'] = true;
                    continue;
                }
                array_push($playListArray['media'], $videoSequenceResource);

            } elseif ($playListItem instanceof Media) {
                $mediaResource = MediaPlayListResource::make($playListItem);
                if($playListItem->status != MediaStatus::CONVERTED){
                    $playListArray['isPending'] = true;
                    continue;
                }
                array_push($playListArray['media'], $mediaResource);
            }
        }
        return $playListArray;
    }
}
