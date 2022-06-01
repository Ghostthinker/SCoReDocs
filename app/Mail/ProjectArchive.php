<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectArchive extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $projectTitle = $this->data['projectTitle'];
        $redirectUrl = url('/project/'. $this->data['projectId']);
        setlocale(LC_TIME, 'German');
        $date = $this->data['updateDate']->formatLocalized('%d.%m.%Y');
        setlocale(LC_TIME, '');
        return $this->subject("Projekt $projectTitle wurde am $date archiviert")
            ->markdown('mails.project_in_archive')
            ->with([
                'name' => $this->data['name'],
                'title' => $projectTitle,
                'redirectUrl' => $redirectUrl,
            ]);
    }
}
