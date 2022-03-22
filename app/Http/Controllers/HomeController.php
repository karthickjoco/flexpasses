<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Services\VulogApiClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\DateTime;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function profiles()
    {
        //exit;
        die();
        $vulogapiclient= new VulogApiClient();
        $urlvechicle='boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/71aa9ffd-9c66-11eb-9659-1277f7a26b4e';
        $vechicleinfo=$vulogapiclient->getRequest($urlvechicle);
        echo  "Vechicle status:".$vechicleinfo->vehicle_status;
        echo '<br>';
        echo  "isDoorClosed:".$vechicleinfo->isDoorClosed;
        echo '<br>';
        echo  "isDoorLocked:".$vechicleinfo->isDoorLocked;
        exit;
        $date  = new DateTime(now());
        $cur1  = $date->modify('-2 days')->format('Y-m-d');
        $now = new DateTime();
        $cur = $now->modify('+1 day')->format('Y-m-d');
        $uri = 'boapi/proxy/trip/fleets/BEAMBIKE-USNYC/trips/users/13675d16-ee71-4a2a-a088-7f8b0d51b23a/?startDate=2021-12-26T05%3A00%3A00Z&endDate=2100-01-02T04%3A59%3A59Z&sort=date%2Cdesc&&size=200';
        $profiles=$vulogapiclient->getRequest($uri);
        print_r(json_encode($profiles));
        exit;
        Storage::put('profiles.json', json_encode($profiles));
        $content=Storage::get('profiles.json');
        print_r($content);
    }

    public function endongoing()
    {
        die();
        $vulogapiclient=new VulogApiClient();
        $url="boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/book";
        $datas=$vulogapiclient->getRequest($url);
        Storage::put('ongoing.json', json_encode($datas));
        $ongoing = json_decode(Storage::get('ongoing.json'));



        foreach ($ongoing as $going) {
            if ($going->vehicle_status != '5') {
                $starttime=Carbon::parse($going->start_date)->setTimezone('America/New_York');
                $currenttime=Carbon::parse(now());
                $timeinminutes=$starttime->diffInMinutes($currenttime);
                // if($timeinminutes>=env('TRIP_REMAINDER')){
                $urlvechicle='boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/'.$going->id;
                $vechicleinfo=$vulogapiclient->getRequest($urlvechicle);
//                   echo  "Vechicle status:".$vechicleinfo->vehicle_status;
//                   echo '<br>';
//                   echo  "isDoorClosed:".$vechicleinfo->isDoorClosed;
//                   echo '<br>';
//                   echo  "isDoorLocked:".$vechicleinfo->isDoorLocked;
                if ($vechicleinfo->vehicle_status=='0' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn) {
                    echo "BIKE Id:".$vechicleinfo->name." --- locked -- inuse -4";
                    echo '<br>';
                }

                //  }
            } else {
                if ($going->vehicle_status == '5') {
                    $urlvechicle='boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/'.$going->id;
                    $vechicleinfo=$vulogapiclient->getRequest($urlvechicle);
//                    echo  "Vechicle status:".$vechicleinfo->vehicle_status;
//                    echo '<br>';
//                    echo  "isDoorClosed:".$vechicleinfo->isDoorClosed;
//                    echo '<br>';
//                    echo  "isDoorLocked:".$vechicleinfo->isDoorLocked;
                    if ($vechicleinfo->vehicle_status=='5' && $vechicleinfo->isDoorLocked && $vechicleinfo->secureOn) {
                        echo "BIKE Id:".$vechicleinfo->name." --- locked -- unsync";
                        echo '<br>';
                    }
                }
            }
        }
    }

    public function endTrip()
    {
        //  die();
        $vulogapiclient=new VulogApiClient();
        $vid="71993dd1-9c66-11eb-9659-1277f7a26b4e";
        $endtripurl="boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/".$vid."/expert";
        $option['cmd']="Trip Termination";
        //$endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
        try {
            $ontrip['number_of_end_attempts'] = 1;
            $ontrip['trip_released'] = 0;
            $endinfo = json_decode($vulogapiclient->postRequest($endtripurl, $option));
            echo 'test';
            echo 'testline';
            $ontrip['number_of_end_attempts'] = 1;
            $ontrip['succeded_end_attempt'] = 1;
            $ontrip['trip_ended'] = 1;
            echo 'try bloack';
            print_r($ontrip);
            echo 'try bloack end';
        } catch (\Exception $e) {
            echo '<br>';
            echo 'catch block';
            $ontrip['number_of_end_attempts'] = 2;
            $ontrip['succeded_end_attempt'] = 0;
            $ontrip['trip_ended'] = 0;
            print_r($e->getMessage());
        }

        print_r($ontrip);

        if ($endinfo->result=="OK") {
            echo 'success';
        } else {
            echo 'failed';
        }

//        if($endinfo->result=="OK"){
//            echo 'Trip termnation failed';
//        }else{
//            echo 'failed';
//        }
    }

    public function approvedUser()
    {
        die();
        $vulogapiclient=new VulogApiClient();
        $profiles=Profile::skip(0)->take(1000)->get();
        $profiles=Profile::skip(1000)->take(1000)->get();
        $profiles=Profile::skip(2000)->take(1000)->get();
        $profiles=Profile::skip(3000)->take(1000)->get();
        $profiles=Profile::skip(4000)->take(1000)->get();

        $i=0;
        foreach ($profiles as $profile) {
//            $uri = 'boapi/proxy/user/fleets/BEAMBIKE-USNYC/users/'.$profile->userId.'/services' ;
//
//            $profiless = $vulogapiclient->getRequest($uri);
//            //print_r(json_encode($profiless));
//            //print_r($profiless->profiles[0]->services->approved);
//            if(in_array(env('DELIVERY_SERVICE'),$profiless->profiles[0]->services->approved)){
//                echo $profile->userName;
//                echo '<br>';
//            }
            $i++;
        }
        echo $profile->id;
        echo '<br>';
        echo $i;
    }

    public function tripRelease()
    {
        $vulogapi=new VulogApiClient();
        $vid="50ed2464-2fc0-35ad-4eff-ee17f35743a5";
        $orderId="23A36C57C4F5B0484170E424E309BA71";
        $triprelease = "boapi/proxy/fleetmanager/public/fleets/BEAMBIKE-USNYC/vehicles/" .$vid. "/release";
        $option['orderId'] = $orderId;
        $releaseinfo = $vulogapi->postRequest($triprelease, $option);
        print_r($releaseinfo);
        echo '-------------------------------------';
        echo '<br>';
        print_r(json_encode($releaseinfo));
        echo '-------------------------------------';
        echo '<br>';
        print_r(json_decode($releaseinfo));
    }
    public function createticket()
    {
        //   die();
        $vulogapi=new VulogApiClient();
        $ticketurl="boapi/proxy/desk/fleets/BEAMBIKE-USNYC/tickets";
        $option['assignedTo'] = "";
        $option['subject'] = "bike needs visit to resync";
        $option['description'] = "";
        $option['status'] = "NEW";
        $option['priority'] = "CRITICAL";
        $option['groupId'] = null;
        $option['categoryId'] = "27294";
        $option['workedHours'] = 0;
        $option['customerFault'] = false;
        $option['ticketHistory'] =[];
        $option['attachments'] = [];
        $option['attachmentsLoading'] = (object)array() ;
        $option['attachmentsToSave'] =[] ;
        $option['vehicleId'] ="8b09c743-2eda-4051-bc07-d79801570131";
        $option['creatorId'] ="0cbbf725-bc97-4f9a-95f0-77f40b9373bf";
        $ticketstatus=json_decode($vulogapi->postRequest($ticketurl, $option));
        echo $ticketstatus->id;
    }
}
