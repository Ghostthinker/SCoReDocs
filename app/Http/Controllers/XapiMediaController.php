<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Project;
use App\Models\Section;
use App\Services\Xapi\XapiMediaService;
use Illuminate\Http\Request;

class XapiMediaController extends Controller
{
    public function index(Request $request, Project $project, Section $section, Media $media)
    {
        $this->send($request, $media, $section);
    }

    public function only(Request $request, Media $media)
    {
        $this->send($request, $media, null);
    }

    /**
     * @param  Request  $request
     * @param  Media  $media
     * @param  null  $section
     */
    private function send(Request $request, Media $media, $section = null): void
    {
        $data = $request->all();
        $data['id'] = $request->fullUrl() . '/' . $data['action'];
        $data['title'] = $media->caption !== null ? $media->caption : '';
        switch ($data['action']) {
            case 'open':
                XapiMediaService::open($section, $media, $data);
                break;
            case 'play':
                XapiMediaService::play($section, $media, $data);
                break;
            case 'pause':
                XapiMediaService::pause($section, $media, $data);
                break;
            case 'seeked':
                XapiMediaService::seeked($section, $media, $data);
                break;
            case 'ended':
                XapiMediaService::end($section, $media, $data);
                break;
            case 'leave':
                XapiMediaService::leave($section, $media, $data);
                break;
        }
    }
}
