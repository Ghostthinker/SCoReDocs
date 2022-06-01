<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Jenssegers\Agent\Agent;

class MailController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Sends a support request to os ticket
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Client\Response
     */
    public function sendSupportRequest(Request $request)
    {
        // Validate (size is in KB)
        $request->validate([
            'attachment' => 'nullable|max:4096',
        ]);

        $url = config('osticket.os_ticket_url');
        $key = config('osticket.key');
        $deptId = config('osticket.dept_id');
        if (!$url || !$key) {
            Log::error('Can\'t load mail settings from config.');
            abort(500, 'Can\'t load mail settings from config.');
        }
        $email = $request->input('email');
        $message = $request->input('message');
        $siteUrl = $request->input('siteUrl');
        $topicId = (int) $request->input('topicId');
        if ($topicId !== 1 && $topicId !== 3) {
            $topicId = 1;
        }
        $userName = 'Gast';
        $userId = 'Gast';
        if (Auth::check()) {
            $userName = Auth::user()->name;
            $userId = Auth::user()->id;
        }
        $signedAttachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $file->storeAs('bug_attachments', $file->getFilename().'.'.$file->getClientOriginalExtension());
            $signedAttachmentPath = URL::signedRoute('bug_attachments',
                $file->getFilename().'.'.$file->getClientOriginalExtension());
        }
        $message = $this->createMessageFooter([
            'message' => $message,
            'name' => $userName,
            'email' => $email,
            'siteUrl' => $siteUrl,
            'topic' => $topicId === 3 ? 'Inhaltliches' : 'Technisches',
            'userId' => $userId,
            'attachment' => $signedAttachmentPath
        ]);
        $response = Http::withHeaders([
            'Expect' => '',
            'X-API-Key' => $key,
        ])->post($url, [
            'name' => $userName,
            'email' => $email,
            'subject' => 'Support Anfrage '.time(),
            'message' => 'data:text/html;charset=utf-8,'.$message,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'gt_field_project' => 'SCoRe',
            'topicId' => $topicId,
            'deptId' => $deptId,
            'Site' => $siteUrl,
            'attachments' => [],
        ]);
        if (!$response->successful()) {
            Log::error('Error on creating ticket', ['response' => $response]);
            abort(500, 'Error on creating ticket.');
        }
        return $response;
    }

    private function createMessageFooter($data)
    {
        $messageParts = [];
        array_push($messageParts, $data['message']);
        array_push($messageParts, '<br><br>');
        array_push($messageParts, '<b>---- Weitere Informationen ----</b>');
        array_push($messageParts, '');
        array_push($messageParts, '<b>Benutzer: </b>'.$data['name']);
        array_push($messageParts, '<b>E-Mail: </b>'.$data['email']);
        array_push($messageParts, '<b>Seite: </b>'.$data['siteUrl']);
        array_push($messageParts, '<b>Problem-Kategorie: </b>'.$data['topic']);
        array_push($messageParts, '<b>Benutzer-id: </b>'.$data['userId']);
        if ($data['attachment']) {
            array_push($messageParts, '<b>Anhang: </b>'.$data['attachment']);
        }
        array_push($messageParts, '');

        $agent = new Agent();
        array_push($messageParts, '<b>Browser: </b>'.$agent->browser());
        array_push($messageParts, '<b>Browser-Version: </b>'.$agent->version($agent->browser()));
        array_push($messageParts, '<b>OS: </b>'.$agent->platform());
        array_push($messageParts, '<b>OS-Version: </b>'.$agent->version($agent->platform()));
        array_push($messageParts, '<b>Device: </b>'.$agent->device());
        return implode('<br>', $messageParts);
    }
}
