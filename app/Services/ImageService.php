<?php

namespace App\Services;

use App\Models\Section;
use App\Repositories\FileRepositoryInterface;
use App\Repositories\SectionMediaRepositoryInterface;
use App\Repositories\SectionRepositoryInterface;
use App\Services\Xapi\XapiImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ImageService
{

    private $sectionRepository;
    private $mediaRepository;
    private $fileRepository;

    public function __construct(
        SectionRepositoryInterface $sectionRepository,
        SectionMediaRepositoryInterface $repository,
        FileRepositoryInterface $fileRepository
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->mediaRepository = $repository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * Adds Ids to the added images to the ckeditor
     *
     * @param  string  $content  The content of the ckeditor
     * @param  int  $sectionId  The Id of the updated section, is used to generate
     *     a unique id for the section elements
     *
     * @return mixed The new image tag
     */
    public function addIdsToImages($content, $sectionId, $fullUrl, $skipXapi = false)
    {
        $contentNew = $content;
        $section = $this->sectionRepository->findOrFail($sectionId);
        preg_match_all('/(<img[^>]+>)/i', $content, $result);
        if (empty($result)) {
            return $contentNew;
        }
        $dataIds = [];
        $allOldSectionMedia = $this->mediaRepository->getBySection($section->id);
        $allOldSectionMediaRefIds = $allOldSectionMedia->pluck('ref_id')
            ->toArray();

        foreach ($result[0] as $image) {
            preg_match('/ref="(.*?)"/', $image, $match);
            if (empty($match)) {
                $contentNew = $this->parseIdToImage($image, $section, $contentNew, $fullUrl);
            } else {
                $dataIds[] = $match[1];
            }
        }

        $sectionMediaRefIdsDiff = array_diff($allOldSectionMediaRefIds, $dataIds);
        $this->mediaRepository->deleteByRef($sectionMediaRefIdsDiff);

        if(!$skipXapi){
            $this->sendXapiDeleted($fullUrl, $sectionMediaRefIdsDiff, $allOldSectionMedia, $section);
        }


        return $contentNew;
    }

    private function parseIdToImage($image, $section, $content, $fullUrl, $skipXapi = false)
    {
        $type = 'Image';
        if (strpos($image, 'id="Image') !== false) {
            $type = 'Video';
        }
        if (strpos($image, 'id="Annotation') !== false) {
            $type = 'Annotation';
        }
        if (strpos($image, 'id="Sequence') !== false) {
            $type = 'Sequence';
        }

        $imageRef = $type.'-Sec-'.$section->id.'_'.Str::uuid();

        if ($type !== 'Image') {
            $mediableId = $this->getMediaIdFromTag($image);
        } else {
            $mediableId = $this->getFileIdFromTag($image);
        }

        $sectionMedia = $this->mediaRepository->create([
            'ref_id' => $imageRef,
            'type' => $type,
            'section_id' => $section->id,
            'mediable_id' => $mediableId,
        ]);

        if(!$skipXapi){
            $this->sendXapiCreated($fullUrl, $sectionMedia, $section);
        }

        $imageNew = str_replace('<img', '<img ref="'.$imageRef.'"', $image);
        $pos = strpos($content, $image);
        if ($pos !== false) {
            $content = substr_replace($content, $imageNew, $pos, strlen($image));
        }
        return $content;
    }

    /**
     * Sending xAPI Statements for section media delete
     *
     * @param  string  $fullUrl
     * @param  array  $sectionMediaRefIdsDiff
     * @param  Collection  $allOldSectionMedia
     * @param  Section  $section
     */
    private function sendXapiDeleted(
        string $fullUrl,
        array $sectionMediaRefIdsDiff,
        Collection $allOldSectionMedia,
        Section $section
    ) {
        foreach ($sectionMediaRefIdsDiff as $sectionMediaRefId) {
            $deletedSectionMedia = $allOldSectionMedia->where('ref_id', $sectionMediaRefId)
                ->first();
            if ($deletedSectionMedia->type === 'Image') {
                XapiImageService::destroyImage($fullUrl, $deletedSectionMedia, $section);
            } elseif ($deletedSectionMedia->type === 'Video') {
                XapiImageService::destroyVideo($fullUrl, $deletedSectionMedia, $section);
            }
        }
    }

    /**
     * Sending xAPI Statements for section media create
     *
     * @param $fullUrl
     * @param $sectionMedia
     * @param $section
     */
    private function sendXapiCreated($fullUrl, $sectionMedia, $section)
    {
        if ($sectionMedia->type == 'Image') {
            XapiImageService::storeImage($fullUrl, $sectionMedia, $section);
        } elseif ($sectionMedia->type == 'Video') {
            XapiImageService::storeVideo($fullUrl, $sectionMedia, $section);
        }
    }

    /**
     * Getting Id from $tag by seraching for "id" and returning digits within
     * the following id
     *
     * @param $tag
     *
     * @return mixed
     */
    private function getMediaIdFromTag($tag)
    {
        preg_match('/id=\"(\D+\K[\d]*)/', $tag, $result);
        return $result[0];
    }

    /**
     * Retrieving file id by using the src attribute of a $tag
     *
     * @param $tag
     *
     * @return mixed|null
     */
    private function getFileIdFromTag($tag)
    {
        preg_match('/src=\"(\K[^"]*)/', $tag, $result);
        $file = null;

        if (sizeof($result) > 0) {
            $file = $this->fileRepository->getByPath($result[0]);
            if ($file) {
                return $file->id;
            }
        }

        return $file;
    }

}
