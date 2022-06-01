<?php

namespace App\Http\Resources\EP5;

use App\Enums\MediaType;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaPlayListResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        // 360 Video - pass look at parameters
        $attributes_urlecoded = '';
        if ($this->type == MediaType::THREE_SIXTY) {
            $cameraPosition = [
                'camX' => 0,
                'camY' => 0,
                'camZ' => 0,
            ];
            $attributes_urlecoded = urlencode(json_encode($cameraPosition));
        }

        $author = $this->author;
        return [
            'id' => $this->id,
            'title' => $this->caption ? $this->caption : 'Kein Titel',
	        'thumbnail' => $this->previewURL,
            'type' => 'Video',
            'updated_at' => $this->created_at,
            'userdata' => [
                'name' => $author ? $author['name'] : 'Unbekannter Ersteller',
                'uid' => $author ? $author['id'] : '',
                'picture' => $author && $author['image'] ? $author['image'] : '/assets/images/default_user.png',
            ]
        ];
    }
}
