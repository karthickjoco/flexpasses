<?php

namespace App\Console\Commands;
use App\Services\VulogApiClient;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Storage;

class getProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getProfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get user profile information';

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
        $date  = new DateTime(now());
        $cur1  = $date->modify('-2 days')->format('Y-m-d');
	    $now = new DateTime();
        $cur = $now->modify('+1 day')->format('Y-m-d');
        $uri = 'boapi/proxy/user/fleets/BEAMBIKE-USNYC/users?startDate=' . $cur1 . '&endDate=' . $cur . '&size=5000&sort=DESC';
        $uri = 'boapi/proxy/user/fleets/BEAMBIKE-USNYC/users?page=0&size=5000&sort=registrationDate,DESC';
        $vulogApiClient = new VulogAPIClient();
        $usersResult    = $vulogApiClient->getRequest($uri);

        Storage::put('profiles.json', json_encode($usersResult));

        $contents = json_decode(Storage::get('profiles.json'));

        foreach ($contents as $users) {
            $profiles['userId']                       = $users->id;
            $profiles['fleetId']                      = $users->fleetId;
            $profiles['userName']                     = $users->userName;
            $profiles['lastName']                     = $users->lastName;
            $profiles['firstName']                    = $users->firstName;
            $profiles['middleName']                   = $users->middleName;
            $profiles['accountStatus']                = $users->accountStatus;
            $profiles['locale']                       = $users->locale;
            $profiles['registrationDate']             = $users->registrationDate;
            $profiles['birthDate']                    = $users->birthDate;
            $profiles['nationality']                  = $users->nationality;
            $profiles['membershipNumber']             = $users->membershipNumber;
            $profiles['notes']                        = $users->notes;
            $profiles['dataPrivacyConsent']           = $users->dataPrivacyConsent;
            $profiles['dateOfAgreements']             = $users->dateOfAgreements;
            $profiles['dataPrivacyConsentUpdateDate'] = $users->dataPrivacyConsentUpdateDate;
            $profiles['profilingConsent']             = $users->profilingConsent;
            $profiles['profilingConsentUpdateDate']   = $users->profilingConsentUpdateDate;
            $profiles['marketingConsent']             = $users->marketingConsent;
            $profiles['marketingConsentUpdateDate']   = $users->marketingConsentUpdateDate;
            $profiles['updateDate']                   = $users->updateDate;
            $profiles['profileId']                    = $users->profiles[0]->profileId;
            $profiles['profileType']                  = $users->profiles[0]->profileType;
            $profiles['services']                     = json_encode($users->profiles[0]->services);

            DB::table('profiles')
                ->updateOrInsert(
                    ['userId' => $users->id],
                    $profiles
                );

            // }
        }
        echo 'Data imporrted Successfully';
    }
}
