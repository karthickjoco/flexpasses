<?php

namespace App\Console\Commands;

use App\Models\ChargeAmount;
use App\Models\endTrip;
use App\Models\ongoing;
use App\Models\Profile;
use App\Models\tripRemainder;
use App\Models\writeLog;
use App\Services\VulogApiClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class endOngoing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'endOngoing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check and end trip if bike is locked and trip is more than  4 hours, try 2 end trip attempt ,if fails release trip.';

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
        $vulogapiclient = new VulogApiClient();
        $url = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/book";
        $datas = $vulogapiclient->getRequest($url);
        Storage::put('ongoing.json', json_encode($datas));
        $ongoing = json_decode(Storage::get('ongoing.json'));

        //die();
        foreach ($ongoing as $going) {
            $checkexists = endTrip::where('orderId', '=', $going->orderId)->where('vechicleid', '=', $going->id)->where('userId', '=', $going->userId)->first();
            if (is_null($checkexists)) {
                if ($going->vehicle_status != '5') {
                    $starttime = Carbon::parse($going->start_date)->setTimezone('America/New_York');
                    $currenttime = Carbon::parse(now());
                    $timeinminutes = $starttime->diffInMinutes($currenttime);
                    if ($timeinminutes >= env('TRIP_REMAINDER')) {
                        $urlvechicle = 'boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/' . $going->id;
                        $vechicleinfo = $vulogapiclient->getRequest($urlvechicle);
                        if ($vechicleinfo->vehicle_status != '5' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn) {
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

                            try {
                                //try to end trip code here
                                $endtripurl = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" . $going->id . "/expert";
                                $option['cmd'] = "Trip Termination";
                                $endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
                                if ($endinfo->result == "OK") {
                                    $ontrip['number_of_end_attempts'] = 1;
                                    $ontrip['succeded_end_attempt'] = 1; // for inuse trip initially zero if the trip end succeeded then we will update it number_of_end_attempts count
                                    $ontrip['trip_ended'] = 1;
                                    $ontrip['trip_released'] = 0;
                                } else {
                                    $ontrip['number_of_end_attempts'] = 1;
                                    $ontrip['trip_ended'] = 0;
                                    $ontrip['succeded_end_attempt'] = 0;
                                    $ontrip['trip_released'] = 0;
                                }
                            } catch (\Exception $e) {
                                $ontrip['number_of_end_attempts'] = 1;
                                $ontrip['trip_ended'] = 0;
                                $ontrip['succeded_end_attempt'] = 0;
                                $ontrip['trip_released'] = 0;
                            }

                            $ontrip['created_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');
                            DB::table('end_trips')
                                ->updateOrInsert(
                                    ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                    $ontrip
                                );
                            $log=new writeLog();
                            $log->bikeid= $going->id;
                            $log->tripid= $going->orderId;
                            $log->action= 'Trip End';
                            $log->save();
                        }
                    }
                }
            } else {
                if ($going->vehicle_status != '5') {
                    $existingdata = endTrip::where('orderId', '=', $going->orderId)->where('vechicleid', '=', $going->id)->where('userId', '=', $going->userId)->where('trip_released', '=', '0')->where('trip_ended', '=', '0')->first();
                    if (!is_null($existingdata)) {
                        $starttime = Carbon::parse($going->start_date)->setTimezone('America/New_York');
                        $currenttime = Carbon::parse(now());
                        $timeinminutes = $starttime->diffInMinutes($currenttime);
                        if ($timeinminutes >= env('TRIP_REMAINDER')) {
                            $urlvechicle = 'boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/' . $going->id;
                            $vechicleinfo = $vulogapiclient->getRequest($urlvechicle);
                            if ($vechicleinfo->vehicle_status != '5' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn) {
                                if ($existingdata->number_of_end_attempts < 2) {
                                    try {
                                        //try to end trip code here
                                        $endtripurl = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" . $going->id . "/expert";
                                        $option['cmd'] = "Trip Termination";
                                        $endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
                                        if ($endinfo->result == "OK") {
                                            $ontrip['number_of_end_attempts'] = $existingdata->number_of_end_attempts + 1;
                                            $ontrip['succeded_end_attempt'] = $ontrip['number_of_end_attempts']; // for inuse trip initially zero if the trip end succeeded then we will update it number_of_end_attempts count
                                            $ontrip['trip_ended'] = 1;
                                            $ontrip['trip_released'] = 0;
                                        } else {
                                            $ontrip['number_of_end_attempts'] = $existingdata->number_of_end_attempts + 1;
                                            $ontrip['trip_ended'] = 0;
                                            $ontrip['succeded_end_attempt'] = 0;
                                            $ontrip['trip_released'] = 0;
                                        }
                                    } catch (\Exception $e) {
                                        $ontrip['number_of_end_attempts'] = $existingdata->number_of_end_attempts + 1;
                                        $ontrip['trip_ended'] = 0;
                                        $ontrip['succeded_end_attempt'] = 0;
                                        $ontrip['trip_released'] = 0;
                                    }
                                    $ontrip['updated_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');
                                    DB::table('end_trips')
                                        ->updateOrInsert(
                                            ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                            $ontrip
                                        );
                                    $log=new writeLog();
                                    $log->bikeid= $going->id;
                                    $log->tripid= $going->orderId;
                                    $log->action= 'Trip End';
                                    $log->save();
                                } else {
                                    try {
                                        $triprelease = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" . $going->id . "/release";
                                        $option['orderId'] = $going->orderId;
                                        $endinfo = $vulogapiclient->postRequest($triprelease, $option);
                                        $ontrip['trip_released'] = 1;
                                        $ontrip['trip_ended'] = 0;
                                        $ontrip['succeded_end_attempt'] = 0;
                                    } catch (\Exception $e) {
                                        $ontrip['trip_released'] = 0;
                                        $ontrip['trip_ended'] = 0;
                                        $ontrip['succeded_end_attempt'] = 0;
                                    }

                                    $ontrip['updated_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');
                                    DB::table('end_trips')
                                        ->updateOrInsert(
                                            ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                            $ontrip
                                        );

                                    $log=new writeLog();
                                    $log->bikeid= $going->id;
                                    $log->tripid= $going->orderId;
                                    $log->action= 'Trip Release';
                                    $log->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 'Successfully ';
    }
}
