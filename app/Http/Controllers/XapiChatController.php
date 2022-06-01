<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Section;
use App\Services\Xapi\XapiChatService;
use Illuminate\Http\Request;

class XapiChatController
{
    public function index(Request $request, Project $project)
    {
        $data = $request->all();
        $data['fullUrl'] = $request->fullUrl() . '/' . $data['action'];
        $section_id = $data['section'];
        $section = Section::find($section_id);

        if ($section !== null) {
            $parent = $section;
            $parentId = url('/sectionId/' . $section->id);
        } else {
            $parent = $project;
            $parentId = url('/projectId/' . $project->id);
        }

        $data['parentId'] = $parentId;
        switch ($data['action']) {
            case 'typing':
                $data['title'] = ['en-US' => 'message in chat: ' . $parent->title];
                XapiChatService::typing($parent, $data, $project->id);
                break;
            case 'send':
                $data['title'] = ['en-US' => 'message in chat: ' . $parent->title];
                XapiChatService::send($parent, $data, $project->id);
                break;
            case 'open':
                $data['title'] = ['en-US' => 'chat: ' . $parent->title];
                XapiChatService::open($parent, $data, $project->id);
                break;
            case 'close':
                $data['title'] = ['en-US' => 'chat left: ' . $parent->title];
                XapiChatService::left($parent, $data, $project->id);
                break;
            case 'switch':
                if($data['oldSectionId']){
                    $data['oldChatId'] = $data['oldSectionId'];
                    $data['oldChatType'] = 'Section';
                    $data['newChatId'] = $project->id;
                    $data['newChatType'] = 'Project';
                }else{
                    $data['oldChatId'] = $project->id;
                    $data['oldChatType'] = 'Project';
                    $data['newChatId'] = $section->id;
                    $data['newChatType'] = 'Section';
                }

                $data['title'] = ['en-US' => 'switched chat'];
                XapiChatService::switched($parent, $data, $project->id);
                break;
        }
    }
}
