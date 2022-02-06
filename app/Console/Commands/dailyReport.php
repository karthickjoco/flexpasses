<?php

namespace App\Console\Commands;

use App\Models\completed;
use App\Models\ongoing;
use App\Services\VulogApiClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class dailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send email to matt.klein@jokr.it & Bcc:jonny@ridejoco.com, jonathan@ridejoco.com, daniel@ridejoco.com daily at 12:01 AM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $date= Carbon::now()->subtract('1 day')->format('Y-m-d');

       //DB::enableQueryLog();
       $select=completed::join('profiles','completeds.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','completeds.vehicleId')->whereDate('completeds.date','=',$date)->get(['vechiles.name','completeds.date as startdate','profiles.userName']);
        //print_r(json_encode($select));
        $select2=ongoing::join('profiles','ongoings.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','ongoings.vechicleid')->whereDate('ongoings.booking_date','=',$date)->whereNotIn('ongoings.orderId',function($query) use ($date){
            $query->select('tripId')->from('completeds')->whereDate('date','=',$date);
        })->get(['vechiles.name','ongoings.booking_date as startdate','profiles.userName']);
        //print_r(json_encode($select2));
        $result=$select2->concat($select);
        //dd(DB::getQueryLog()); // Show results of log
        if(count($result)) {
            Mail::to('karthick@ridejoco.com')->send(new \App\Mail\dailyReport($result));
        }

    }
}
