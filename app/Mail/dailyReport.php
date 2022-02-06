<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class dailyReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $details;
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date=Carbon::now()->subtract('1 day')->format('m/d/Y');
        return $this->subject('JOCO Flex Pass Trip Summary - '.$date)->from('support@ridejoco.com','JOCO')->view('email.dailyreport');
        //return $this->view('view.name');
    }
}
