<?php

namespace App\Console\Commands;

use App\Models\ChargeAmount;
use App\Models\Profile;
use App\Services\VulogApiClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class getCompletedTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getCompletedTrips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getCompletedTrips';

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
        $vulogapiclient=new VulogApiClient();
        $selecuser=Profile::where('userName','like','D000%')->get();
        //$datestart=Carbon::now()->setTimezone('UTC')->subtract('5 minute')->format('Y-m-d H:i:s');//2022-02-06T11:06:00Z
        $datestart=Carbon::now()->setTimezone('UTC')->subtract('6 minute')->toIso8601ZuluString();//2022-02-06T11:06:00Z
        $dateend=Carbon::now()->setTimezone('UTC')->toIso8601ZuluString();
        //2022-02-01T05%3A00%3A00Z
        foreach ($selecuser as $user) {
            $url = "boapi/proxy/trip/fleets/BEAMBIKE-USNYC/trips/users/" . $user->userId . "/?startDate=".$datestart."&endDate=".$dateend."&sort=date,desc&size=2000";
            $completed = $vulogapiclient->getRequest($url);
            Storage::put('completed.json', json_encode($completed));
            $contents = json_decode(Storage::get('completed.json'));

            foreach ($completed as $complet) {

                $complete['completedid'] = $complet->id;
                $complete['tripId'] = $complet->tripId;
                $complete['fleetId'] = $complet->fleetId;
                $complete['userId'] = $complet->userId;
                $complete['profileId'] = $complet->profileId;
                $complete['profileType'] = $complet->profileType;
                $complete['vehicleId'] = $complet->vehicleId;
                $complete['length'] = $complet->length;
                $complete['duration'] = $complet->duration;
                $complete['pauseDuration'] = $complet->pauseDuration;
                $complete['tripDuration'] = $complet->tripDuration;
                $complete['bookingDuration'] = $complet->bookingDuration;
                $complete['drivingDuration'] = $complet->drivingDuration;
                $complete['date'] = $complet->date;
                $complete['endDate'] = $complet->endDate;
                $complete['additionalInfo'] = json_encode($complet->additionalInfo);
                $complete['pricingId'] = $complet->pricingId;
                $complete['productIds'] = json_encode($complet->productIds);
                $complete['serviceId'] = $complet->serviceId;
                $complete['serviceType'] = $complet->serviceType;
                $complete['theorStartDate'] = $complet->theorStartDate;
                $complete['theorEndDate'] = $complet->theorEndDate;
                $complete['ticketInfo'] = json_encode($complet->ticketInfo);
                $complete['tripEvents'] = json_encode($complet->tripEvents);


                $completedtrip = DB::table('completeds')->where('tripId', '=', $complet->tripId)->first();

                if ($completedtrip === null) {
                    // user doesn't exist
                    DB::table('completeds')
                        ->insert($complete);
                }

                // }
            }
        }
        echo 'Data imporrted Successfully';

    }
}
