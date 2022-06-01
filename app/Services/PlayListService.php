<?php

namespace App\Services;

use App\Repositories\EP5\VideoSequenceRepositoryInterface;
use App\Repositories\MediaRepositoryInterface;

class PlayListService
{
    private $sequenceRepository;
    private $mediaRepository;

    public function __construct(
        VideoSequenceRepositoryInterface $sequenceRepository,
        MediaRepositoryInterface $mediaRepository
    )
    {
        $this->sequenceRepository = $sequenceRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Create a playlist array from all sequences and videos of a section
     *
     * @param $content
     * @return mixed The new image tag
     */
    public function createPlayListArray($content)
    {
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>|(<img[^>]+>)/i', $content, $result);
        $playListItems = [];
        if (empty($result)) {
            return $playListItems;
        }
        foreach ($result[0] as $image) {
            if (strpos($image, 'id="Image') !== false) {
                preg_match_all('/id="(\d+)"/', str_replace('Image', '', $image), $matches);
                array_push($playListItems,
                    $this->mediaRepository->get($matches[1][0])
                );
            }
            if (strpos($image, 'id="Sequence') !== false) {
                preg_match_all('/Sequence(\d*)Media/', $image, $matches);
                array_push($playListItems,
                    $this->sequenceRepository->get($matches[1][0])
                );
            }
        }

        return $playListItems;
    }
}
