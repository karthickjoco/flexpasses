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

class monthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthlyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '20th of Month to 19th of Month +1 send email to jonny@ridejoco.com, jonathan@ridejoco.com, daniel@ridejoco.com  ';

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
        $datelastmonth= Carbon::now()->subtract('1 month')->format('Y-m-d');
        $datecurrentmonth= Carbon::now()->subtract('1 day')->format('Y-m-d');


       $select=completed::join('profiles','completeds.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','completeds.vehicleId')->whereDate('completeds.date','>=',$datelastmonth)->whereDate('completeds.date','<=',$datecurrentmonth)->get(['vechiles.name','completeds.date as startdate','profiles.userName']);
        //print_r(json_encode($select));
        //DB::enableQueryLog();
        $select2=ongoing::join('profiles','ongoings.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','ongoings.vechicleid')->whereDate('ongoings.booking_date','>=',$datelastmonth)->whereDate('ongoings.booking_date','<=',$datecurrentmonth)->whereNotIn('ongoings.orderId',function($query) use ($datelastmonth,$datecurrentmonth){
            $query->select('tripId')->from('completeds')->whereDate('date','>=',$datelastmonth)->whereDate('date','<=',$datecurrentmonth);
        })->get(['vechiles.name','ongoings.booking_date as startdate','profiles.userName']);
        //dd(DB::getQueryLog());
        //print_r(json_encode($select2));
        $result=$select2->concat($select);

        //dd(DB::getQueryLog()); // Show results of log
        if(count($result)) {
            Mail::to('karthick@ridejoco.com')->bcc('jonathan@ridejoco.com')->send(new \App\Mail\monthlyReport($result));
        }

    }
}
