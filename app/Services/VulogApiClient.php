<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Guzzle\Stream\PhpStreamRequestFactory;

use App\Models\User;

class VulogApiClient
{
    protected $client;
    protected $api_url;
    protected $client_id;
    protected $client_secret;
    protected $username;
    protected $password;
    protected $headers;
    public $accessToken;
    private $securityOptions;
    protected $fleetid='BEAMBIKE-USNYC';
    public function __construct()
    {
        $this->client = new Client(['defaults' => [
            'verify' => false
        ]]);
        $this->prepareHeaders();
        $this->callBack();
    }

    public function prepareHeaders()
    {
        //staging
        $this->api_url         = 'https://java-sta.vulog.com/';
        $this->client_id       = 'BEAMBIKE-USNYC_secure';
        $this->client_secret   = 'a0482880-5e33-4145-a54f-37f95df250fb';
        $this->securityOptions = 'SSL_OP_NO_SSLv3';
        $this->username        = 'karthick';
        $this->password        = 'Abc123!';
        $this->apikey          = 'ed1afac2-093d-46d8-96e8-363ecde45c34';

        //  production
        // $this->api_url 			= 'https://java-us01.vulog.com/';
        // $this->client_id 		= 'BEAMBIKE-USNYC_secure';
        // $this->client_secret 	= '436a4c9e-ca50-489f-9545-83c5bf7eab84';
        // $this->securityOptions 	= 'SSL_OP_NO_SSLv3';
        // $this->username 		= 'karthick@parliamenttutors.com';
        // $this->password 		= 'Ridejoco2020!';
        // $this->apikey 			= 'ffe12241-2f42-44c7-a892-49aff4153d15';
    }

    public function prepareBearerHeaders()
    {
        $headersInfo = [
            'User-Agent' 			=> 'testing/1.0',
            'Accept'    		 	=> 'application/json',
            'Content-Type'    		=> 'application/json',
            'Authorization'   	    => 'Bearer '.$this->accessToken,
            'x-api-key'   	    	=> $this->apikey,

        ];

        $this->headers	=	$headersInfo;

        $csvheadersInfo = [
            'User-Agent' 			=> 'testing/1.0',
            'Accept' => 'text/csv',
            'Content-Type'    		=> 'application/json',
            'Authorization'   	    => 'Bearer '.$this->accessToken,
            'x-api-key'   	    	=> $this->apikey
        ];

        $this->csvheaders	=	$csvheadersInfo;
    }


    public function getRequest(string $uri = null, array $query = [])
    {
        $this->prepareBearerHeaders();
        $full_path = $this->api_url;
        $full_path .= $uri;

        try {
            $request = $this->client->get($full_path, [
                'headers'         => $this->headers,
                //'body'=>json_encode($query)

            ]);

            $response = $request ? $request->getBody()->getContents() : null;
            $status = $request ? $request->getStatusCode() : 500;

            if ($response && $status === 200 && $response !== 'null') {
                return json_decode($response);
            }
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            #guzzle repose for future use
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r($responseBodyAsString);
        }

        return null;
    }

    public function postRequest(string $uri = null, array $post_params = [])
    {
        $this->prepareBearerHeaders();
        $full_path = $this->api_url;
        $full_path .= $uri;
        //    print_r(json_encode($post_params));exit;
        //print_r($post_params);exit;
        try {
            $request = $this->client->post($full_path, [
                    'headers' => $this->headers,
                    'body' => json_encode($post_params),
                    //'http_errors'=>false
                ]);


            $response = $request ? $request->getBody()->getContents() : null;


            $status = $request ? $request->getStatusCode() : 500;
            //print_r($status);exit;
            if ($response && $status === 200 && $response !== 'null') {
                return $response;
            }
        } catch (Guzzle\Http\Exception\BadResponseException $e) {
            #guzzle repose for future use
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r($responseBodyAsString);
        }
        return null;
    }

    public function deleteRequest(string $uri = null, array $post_params = [])
    {
        $this->prepareBearerHeaders();
        $full_path = $this->api_url;
        $full_path .= $uri;

        try {
            $request = $this->client->delete($full_path, [
                'headers' => $this->headers,
                'body'    => json_encode($post_params),
            ]);

            $response = $request ? $request->getBody()->getContents() : null;

            $status = $request ? $request->getStatusCode() : 500;

            if ($response && $status === 204 && $response !== 'null') {
                return $response;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            #guzzle repose for future use
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r($responseBodyAsString);
        }
        return null;
    }

    public function postCsvRequest(string $uri = null, $post_params)
    {
        $this->prepareBearerHeaders();
        $full_path = $this->api_url;
        $full_path .= $uri;
        //print_r($this->headers);
        try {
            $request = $this->client->post($full_path, [
                'headers'         => $this->headers,
                'stream' => true,
                'body' => json_encode([
                    'date' => $post_params,
                ])
            ]);

            //$response = $request ? $request->getBody() : null;

            $response = $request ? $request->getBody()->getContents() : null;


            $status = $request ? $request->getStatusCode() : 500;

            if ($response && $status === 200 && $response !== 'null') {
                return $response;
            }
        } catch (GuzzleHttp\Exception\BadResponseException $e) {
            #guzzle repose for future use
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            print_r($responseBodyAsString);
        }
        return null;
    }
    public function refreshToken()
    {
        $full_path	 = $this->api_url;
        $full_path .= 'auth/realms/'.$this->fleetid.'/protocol/openid-connect/token';


        $refresh_token	= auth()->user()->token->refresh_token;

        $postparm	= [
            'grant_type'	 	=> 'refresh_token',
            'client_id' 		=> $this->client_id,
            'client_secret' 	=> $this->client_secret,
            'securityOptions'	=> $this->securityOptions,
            'refresh_token' 	=> $refresh_token
        ];

        $request = $this->client->post($full_path, [
            'form_params'     => $postparm
        ]);

        $response = $request ? $request->getBody()->getContents() : null;
        $status = $request ? $request->getStatusCode() : 500;

        if ($response && $status === 200 && $response !== 'null') {
            $res= json_decode($response);

            $this->accessToken = $res->access_token;
            $expires_in			= $res->expires_in;
            $final_expires_in   = $expires_in-10;
            auth()->user()->token()->update([
                'access_token'	=> $res->access_token,
                'expires_in' 	=> $final_expires_in,
                'refresh_token' => $res->refresh_token
            ]);
        }
    }

    public function callBack()
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', 1)->first();
            Auth::login($user);
        }

        $full_path	 = $this->api_url;
        $full_path .= 'auth/realms/'.$this->fleetid.'/protocol/openid-connect/token';

        if (auth()->user()->token) {
            if (auth()->user()->token->hasExpired()) {
                $this->refreshToken();
            } else {
                $this->accessToken = auth()->user()->token->access_token;
            }
        } else {
            $postparm	= [
                'grant_type'	 	=> 'password',
                'client_id' 		=> $this->client_id,
                'client_secret' 	=> $this->client_secret,
                'securityOptions'	=> $this->securityOptions,
                'username' 			=> $this->username,
                'password'			=> $this->password,
                'apikey' 			=> $this->apikey,
            ];


            $request = $this->client->post($full_path, [
                'form_params'     => $postparm
            ]);

            $response = $request ? $request->getBody()->getContents() : null;
            $status = $request ? $request->getStatusCode() : 500;

            if ($response && $status === 200 && $response !== 'null') {
                $res= json_decode($response);

                $this->accessToken = $res->access_token;
                $expires_in			= $res->expires_in;
                $final_expires_in   = $expires_in-10;
                auth()->user()->token()->create([
                    'access_token' => $res->access_token,
                    'expires_in' => $final_expires_in,
                    'refresh_token' => $res->refresh_token
                ]);
            }
        }
    }

    public function kalviyoapi($uri, $data)
    {
        $user = User::where('id', 1)->first();
        Auth::login($user);
        $headersInfo = [
            'User-Agent'=> 'testing/2.0',
            'Accept'=> 'application/json',
            'Content-Type'=> 'application/json',

        ];
        $request=$this->client->post($uri, [
            'headers'=>$headersInfo,
            'stream'=>true,
            'body'=>json_encode($data)
        ]);
        $response = $request ? $request->getBody()->getContents() : null;
    }
}
