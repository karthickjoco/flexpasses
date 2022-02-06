<?php

namespace App\Console\Commands;

use App\Services\VulogApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class getVechiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getVechiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'getVechicle';

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
        $url="boapi/proxy/vehicle/fleets/BEAMBIKE-USNYC/vehicles/";
        $vechicles=$vulogapiclient->getRequest($url);
        Storage::put('vechile.json', json_encode($vechicles));
        $contents = json_decode(Storage::get('vechile.json'));
//        print_r(json_encode($vechicles));

        foreach ($contents as $vechile) {
            $vechiles['vechicleid']=$vechile->id;
            $vechiles['vin']=$vechile->vin;
            $vechiles['name']=$vechile->name;
            $vechiles['plate']=$vechile->plate;
            $vechiles['model']=json_encode($vechile->model);
            $vechiles['options']=json_encode($vechile->options);
            $vechiles['createDate']=$vechile->createDate;
            $vechiles['fleetId']=$vechile->fleetId;
            if(property_exists($vechile,'vuboxId')) {
                $vechiles['vuboxId'] = $vechile->vuboxId;
            }
            $vechiles['externalId']=$vechile->externalId;
            if(property_exists($vechile,'wakeupProvider')){
                $vechiles['wakeupProvider']=$vechile->wakeupProvider;
            }
            $vechiles['iccid']=$vechile->iccid;
            $vechiles['published']=$vechile->published;
            $vechiles['archived']=$vechile->archived;
            DB::table('vechiles')
                ->updateOrInsert(
                    ['vechicleid' => $vechile->id],
                    $vechiles
                );

            // }
        }
        echo 'Data imporrted Successfully';

    }
}
