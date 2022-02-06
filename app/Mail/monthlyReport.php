<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class monthlyReport extends Mailable
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
        $datelastmonth= Carbon::now()->subtract('1 month')->format('d M Y');
        $datecurrentmonth= Carbon::now()->subtract('1 day')->format('d M Y');
        return $this->subject('A summary of trips taken  from '.$datelastmonth.' to '.$datecurrentmonth)->from('support@ridejoco.com','JOCO')->view('email.monthlyreport');
        //return $this->view('view.name');
    }
}
