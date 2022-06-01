<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssessmentReadyForReview extends Mailable
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
        return $this->subject('Assessment bereit zur Prüfung')
            ->markdown('mails.assessment_ready_for_review')
            ->with([
                'student_name' => $this->data['student_name'],
                'matriculation_number' => $this->data['matriculation_number'],
                'linkToAssessment' => $this->data['linkToAssessment'],
            ]);
    }
}
