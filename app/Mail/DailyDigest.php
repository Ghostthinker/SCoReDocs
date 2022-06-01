<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyDigest extends Mailable
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
        setlocale(LC_TIME, 'German');
        $date = Carbon::now()->formatLocalized('%A, %d.%m.%Y');
        setlocale(LC_TIME, '');
        return $this->subject('SCoRe-Docs Daily Digest - '.$this->data['projectTitle'].' am '.$date)
            ->markdown('mails.daily_digest')
            ->with([
                'name' => $this->data['name'],
                'projectTitle' => $this->data['projectTitle'],
                'dailyDigest' => $this->data['dailyDigest'],
                'text' => $this->data['text'],
                'redirectUrl' => $redirectUrl
            ]);
    }
}
