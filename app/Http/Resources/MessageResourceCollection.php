<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageResourceCollection extends ResourceCollection
{
    /**
     * @var mixed
     */
    protected $actualUser;
    protected $unreadMessagesCount;

    public function actualUser($value)
    {
        $this->actualUser = $value;
        return $this;
    }

    public function toArray($request)
    {
        $data = $this->collection->map(function (MessageResource $resource) use ($request) {
            return $resource->actualUser($this->actualUser)->toArray($request);
        })->all();

        return $data;
    }
}
