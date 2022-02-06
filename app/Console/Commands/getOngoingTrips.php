<?php

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\VulogApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class getOngoingTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getOngoingTrips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getOngoingTrips';

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
            $selecuser=Profile::where('userName','like','D000%')->get(['userId']);

            $userarray=array();
            foreach ($selecuser as $user) {
                $userarray[]=$user->userId;
            }

            $url = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/book";
            $ongoing = $vulogapiclient->getRequest($url);
            Storage::put('ongoingtrips.json', json_encode($ongoing));
            $contents = json_decode(Storage::get('ongoingtrips.json'));

            foreach ($ongoing as $going) {
                if(in_array($going->userId,$userarray)){
                    $ontrip['vechicleid'] = $going->id;
                    $ontrip['name'] = $going->name;
                    $ontrip['plate'] = $going->plate;
                    $ontrip['vin'] = $going->vin;
                    $ontrip['fleetid'] = $going->fleetid;
                    $ontrip['boxid'] = $going->boxid;
                    $ontrip['vehicle_status'] = $going->vehicle_status;
                    $ontrip['zones'] = json_encode($going->zones);
                    $ontrip['releasable'] = $going->releasable;
                    $ontrip['box_status'] = $going->box_status;
                    $ontrip['autonomy'] = $going->autonomy;
                    $ontrip['autonomy2'] = $going->autonomy2;
                    $ontrip['isCharging'] = $going->isCharging;
                    $ontrip['battery'] = $going->battery;
                    $ontrip['km'] = $going->km;
                    $ontrip['speed'] = $going->speed;
                    $ontrip['location'] = json_encode($going->location);
                    $ontrip['endZoneIds'] = json_encode($going->endZoneIds);
                    $ontrip['productIds'] = json_encode($going->productIds);
                    $ontrip['isDoorClosed'] = $going->isDoorClosed;
                    $ontrip['isDoorLocked'] = $going->isDoorLocked;
                    $ontrip['engineOn'] = $going->engineOn;
                    $ontrip['secureOn'] = $going->secureOn;
                    $ontrip['userId'] = $going->userId;
                    $ontrip['user_locale'] = $going->user_locale;
                    $ontrip['rfid'] = $going->rfid;
                    $ontrip['orderId'] = $going->orderId;
                    $ontrip['gatewayUrl'] = $going->gatewayUrl;
                    $ontrip['booking_status'] = $going->booking_status;
                    $ontrip['booking_date'] = $going->booking_date;
                    $ontrip['expiresOn'] = $going->expiresOn;
                    $ontrip['last_active_date'] = $going->last_active_date;
                    $ontrip['last_wakeUp_date'] = $going->last_wakeUp_date;
                    $ontrip['version'] = $going->version;
                    $ontrip['pricingId'] = $going->pricingId;
                    $ontrip['start_date'] = $going->start_date;
                    $ontrip['theorStartDate'] = $going->theorStartDate;
                    $ontrip['theorEndDate'] = $going->theorEndDate;
                    $ontrip['startZones'] = json_encode($going->startZones);
                    $ontrip['endZones'] = json_encode($going->endZones);
                    $ontrip['disabled'] = $going->disabled;
                    $ontrip['outOfServiceReason'] = $going->outOfServiceReason;
                    $ontrip['geohashNeighbours'] = json_encode($going->geohashNeighbours);
                    $ontrip['cleanlinessStatus'] = $going->cleanlinessStatus;
                    $ontrip['needsRedistribution'] = $going->needsRedistribution;
                    $ontrip['batteryUnderThreshold'] = $going->batteryUnderThreshold;
                    $ontrip['isBeingTowed'] = $going->isBeingTowed;
                    $ontrip['automaticallyEnableVehicleAfterRangeRecovery'] = $going->automaticallyEnableVehicleAfterRangeRecovery;
                    $ontrip['key'] = json_encode($going->key);
                    $ontrip['startTripLocation'] = json_encode($going->startTripLocation);
                    $ontrip['username'] = $going->username;
                    $ontrip['profileType'] = $going->profileType;
                    $ontrip['comeFromApp'] = $going->comeFromApp;
                    $ontrip['serviceId'] = $going->serviceId;
                    $ontrip['serviceType'] = $going->serviceType;
                    $ontrip['profileId'] = $going->profileId;
                    $ontrip['preAuthStatus'] = $going->preAuthStatus;
                    $ontrip['start_mileage'] = $going->start_mileage;
                    $ontrip['preAuthEnabled'] = $going->preAuthEnabled;
                    $ontrip['entityId'] = $going->entityId;
                    if (property_exists($going, 'amountPreAuth')) {
                        $ontrip['amountPreAuth'] = $going->amountPreAuth;
                    }

                    DB::table('ongoings')
                        ->updateOrInsert(
                            ['orderId' => $going->orderId],
                            $ontrip
                        );

                 }
            }

        echo 'Data imporrted Successfully';

    }
}
