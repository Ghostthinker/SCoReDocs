<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssessmentReviewed extends Mailable
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
        $redirectUrl = url('/project/'.$this->data['projectId']);
        return $this->subject('Assessment wurde geprüft')
            ->markdown('mails.assessment_reviewed')
            ->with([
                'name' => $this->data['name'],
                'projectId' => $this->data['projectId'],
                'redirectUrl' => $redirectUrl,
            ]);
    }
}
