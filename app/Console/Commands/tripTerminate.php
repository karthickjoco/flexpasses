<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\endTrip;
use App\Models\ongoing;
use App\Models\Profile;
use App\Models\writeLog;
use App\Models\ChargeAmount;
use App\Models\tripRemainder;
use App\Models\tripTermination;
use Illuminate\Console\Command;
use App\Services\VulogApiClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class tripTerminate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tripTerminate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' a unsync trip with bike locked status ';

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
            $checkexists = tripTermination::where('orderId', '=', $going->orderId)->where('vechicleid', '=', $going->id)->where('userId', '=', $going->userId)->first();
            if (is_null($checkexists)) {
                if ($going->vehicle_status == '5') {
                    $urlvechicle = 'boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/' . $going->id;
                    $vechicleinfo = $vulogapiclient->getRequest($urlvechicle);
                    if ($vechicleinfo->vehicle_status == '5' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn) {
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
                            //try to terminate trip code here
                            $endtripurl = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" . $going->id . "/expert";
                            $option['cmd'] = "Trip Termination";
                            $endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
                            if ($endinfo->result == "OK") {
                                $ontrip['number_of_trip_terminate_attempt'] = 1;
                                $ontrip['succeded_trip_terminate_attempt'] = 1; // for inuse trip initially zero if the trip end succeeded then we will update it number_of_end_attempts count
                                $ontrip['trip_terminated'] = 1;
                                $ontrip['ticket_created'] = 0;
                            } else {
                                $ontrip['number_of_trip_terminate_attempt'] = 1;
                                $ontrip['succeded_trip_terminate_attempt'] = 0;
                                $ontrip['trip_terminated'] = 0;
                                $ontrip['ticket_created'] = 0;
                            }
                        } catch (\Exception $e) {
                            $ontrip['number_of_trip_terminate_attempt'] = 1;
                            $ontrip['succeded_trip_terminate_attempt'] = 0;
                            $ontrip['trip_terminated'] = 0;
                            $ontrip['ticket_created'] = 0;
                        }
                        $ontrip['created_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');

                        DB::table('trip_terminations')
                                ->updateOrInsert(
                                    ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                    $ontrip
                                );

                        $log=new writeLog();
                        $log->bikeid= $going->id;
                        $log->tripid= $going->orderId;
                        $log->action= 'Trip Terminate';
                        $log->save();
                    }
                }
            } else {
                if ($going->vehicle_status == '5') {
                    $existingdata = tripTermination::where('orderId', '=', $going->orderId)->where('vechicleid', '=', $going->id)->where('userId', '=', $going->userId)->where('trip_terminated', '=', '0')->where('ticket_created', '=', '0')->first();

                    $urlvechicle = 'boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/' . $going->id;
                    $vechicleinfo = $vulogapiclient->getRequest($urlvechicle);
                    if ($vechicleinfo->vehicle_status == '5' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn && !is_null($existingdata)) {
                        if ($existingdata->number_of_trip_terminate_attempt < 5) {
                            $ontrip['number_of_trip_terminate_attempt'] = $existingdata->number_of_trip_terminate_attempt + 1;
                            $ontrip['succeded_trip_terminate_attempt'] = 0; // for inuse trip initially zero if the trip end succeeded then we will update it number_of_end_attempts count

                            try {
                                //try to end trip code here
                                $endtripurl = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" . $going->id . "/expert";
                                $option['cmd'] = "Trip Termination";
                                $endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
                                if ($endinfo->result == "OK") {
                                    $ontrip['number_of_trip_terminate_attempt'] = $existingdata->number_of_trip_terminate_attempt + 1;
                                    $ontrip['succeded_trip_terminate_attempt'] = $ontrip['number_of_trip_terminate_attempt']; // for inuse trip initially zero if the trip end succeeded then we will update it number_of_end_attempts count
                                    $ontrip['trip_terminated'] = 1;
                                    $ontrip['ticket_created'] = 0;
                                } else {
                                    $ontrip['number_of_trip_terminate_attempt'] = $existingdata->number_of_trip_terminate_attempt + 1;
                                    $ontrip['trip_terminated'] = 0;
                                    $ontrip['succeded_trip_terminate_attempt'] = 0;
                                    $ontrip['ticket_created'] = 0;
                                }
                            } catch (\Exception $e) {
                                $ontrip['number_of_trip_terminate_attempt'] = $existingdata->number_of_trip_terminate_attempt + 1;
                                $ontrip['trip_terminated'] = 0;
                                $ontrip['succeded_trip_terminate_attempt'] = 0;
                                $ontrip['ticket_created'] = 0;
                            }
                            $ontrip['updated_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');
                            DB::table('trip_terminations')
                                    ->updateOrInsert(
                                        ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                        $ontrip
                                    );
                            $log=new writeLog();
                            $log->bikeid= $going->id;
                            $log->tripid= $going->orderId;
                            $log->action= 'Trip Terminate';
                            $log->save();
                        } else {
                            //create ticket here against the bike

                            try {
                                $ticketurl = "boapi/proxy/desk/fleets/BEAMBIKE-USNYC/tickets";
                                $option['assignedTo'] = "";
                                $option['subject'] = "bike needs visit to resync";
                                $option['description'] = "";
                                $option['status'] = "NEW";
                                $option['priority'] = "CRITICAL";
                                $option['groupId'] = null;
                                //$option['categoryId'] = "27294";
                                $option['categoryId'] = "2701";
                                $option['workedHours'] = 0;
                                $option['customerFault'] = false;
                                $option['ticketHistory'] = [];
                                $option['attachments'] = [];
                                $option['attachmentsLoading'] = (object)array();
                                $option['attachmentsToSave'] = [];
                                $option['vehicleId'] = $going->id;
                                //  $option['creatorId'] = "0cbbf725-bc97-4f9a-95f0-77f40b9373bf";
                                $option['creatorId'] = "c83ce49a-4e40-4a04-a981-f0501ccfaa28";

                                $ticketstatus=json_decode($vulogapiclient->postRequest($ticketurl, $option));
                                $ticketid=$ticketstatus->id;
                                $ontrip['trip_terminated'] = 0;
                                $ontrip['succeded_trip_terminate_attempt'] = 0;
                                $ontrip['ticket_created'] = 1;
                            } catch (\Exception $e) {
                                $ontrip['trip_terminated'] = 0;
                                $ontrip['succeded_trip_terminate_attempt'] = 0;
                                $ontrip['ticket_created'] = 0;
                                $ticketid="NA";
                            }

                            $ontrip['updated_at'] = Carbon::parse(now())->format('Y-m-d H:i:s');
                            DB::table('trip_terminations')
                                    ->updateOrInsert(
                                        ['orderId' => $going->orderId, 'vechicleid' => $going->id, 'userId' => $going->userId],
                                        $ontrip
                                    );

                            $log=new writeLog();
                            $log->bikeid= $going->id;
                            $log->tripid= $going->orderId;
                            $log->ticketid=$ticketid;
                            $log->action= 'Ticket Creation';
                            $log->save();
                        }
                    }
                }
            }
        }
        // echo 'Data imporrted Successfully';
    }
}
