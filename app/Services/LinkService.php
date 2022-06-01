<?php

namespace App\Services;

use App\Models\Section;
use App\Repositories\LinkRepositoryInterface;
use App\Services\Xapi\XapiLinkService;

class LinkService
{
    private $linkRepository;

    public function __construct(LinkRepositoryInterface $repository)
    {
        $this->linkRepository = $repository;
    }

    /**
     * Parsing the links in the ckeditor content. Method checks for local links and writes them to db.
     *
     * @param  string|null  $content  The content of the ckeditor
     * @param  Section  $section
     * @param  int  $projectId  The Id of the section's project
     * @param  string  $requestUrl
     *
     * @param  bool  $skipXapi
     * @return string|null newContent
     */
    public function parseLocalLinks(?string $content, Section $section, int $projectId, string $requestUrl, $skipXapi = false): ?string
    {
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $content, $result);
        $contentNew = $content;
        $dataIds = [];
        $allOldLinks = $this->linkRepository->getBySection($section->id);
        $allOldLinksIds = $allOldLinks->pluck('id')->toArray();
        if (!empty($result)) {
            $localLinks = $this->createLocalLinks($projectId, $section->id, $result);

            foreach ($localLinks as $key => $link) {
                preg_match('/data-db-id="(.*?)"/', $link['originHref'], $match);
                if (empty($match)) {
                    $originHref = $link['originHref'];
                    unset($link['originHref']);
                    $id = $this->linkRepository->insertRecordAndGetId($link);
                    $link['id'] = $id;
                    if(!$skipXapi){
                        XapiLinkService::createLink($requestUrl, $link, $section);
                    }

                    $contentNew = $this->addLinkIdsToContent($originHref, $id, $contentNew);
                } else {
                    $dataIds[] = $match[1];
                }
            }

            $linkIdsDiff = array_diff($allOldLinksIds, $dataIds);
            $this->linkRepository->deleteIds($linkIdsDiff);

            if(!$skipXapi) {
                foreach ($linkIdsDiff as $linkId) {
                    $deletedLink = $allOldLinks->where('id', $linkId)->first()->toArray();
                    XapiLinkService::destroyLink($requestUrl, $deletedLink, $section);
                }
            }
        }
        return $contentNew;
    }

    /**
     * Method creates the local link out of the result href and the ids
     *
     * @param int $projectId The project id
     * @param int $sectionId The section id
     * @param string[] $foundHrefs The hrefs
     *
     * @param string $content
     * @return array The data of the parsed link
     */
    private function createLocalLinks(int $projectId, int $sectionId, array $foundHrefs)
    {
        $data = [];
        $projectUrl = url('/project/');
        $origin = $projectUrl . '/' . $projectId . '#Section-' . $sectionId;
        foreach ($foundHrefs['href'] as $key => $href) {
            if (strpos($href, $projectUrl) !== false) {
                $hrefParts = explode('#', $href);
                if (count($hrefParts) > 1) {
                    $hrefHash = $hrefParts[1];
                    $types = explode('-', $hrefHash);

                    $data[$key] = [
                        'ref_id' => $hrefHash,
                        'target' => $href,
                        'origin' => $origin,
                        'type' => $types[0] !== null ? $types[0] : 'None',
                        'section_id' => $sectionId,
                        'created_at' => now(),
                        'originHref' => $foundHrefs[0][$key],
                    ];
                }
            }
        }
        return $data;
    }

    private function addLinkIdsToContent($link, $id, $content)
    {
        $linkNew = str_replace('<a', '<a data-db-id="' . $id . '"', $link);

        $pos = strpos($content, $link);
        if ($pos !== false) {
            $content = substr_replace($content, $linkNew, $pos, strlen($link));
        }
        return $content;
    }
}
