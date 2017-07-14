<?php

namespace App\Http\Controllers;

use App\TokenStorage;
use App\Http\Requests;
use App\Http\Controllers\Auth;
use App\User;
use League\OAuth2\Client\Token\AccessToken;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


	public function getAccessTokenFromDataBase(){

		$saved_token = TokenStorage::orderBy('id', 'desc')->first();
		if (!$saved_token) return null;
		$serialized_token = $saved_token->object;
		if ($serialized_token) {
			$token = unserialize($serialized_token);
			return $token;
		}

		return null;

	}

	public function saveAccessTokenToDataBase(AccessToken $token){

		//$token_array = ['access_token' => $token->getToken(), 'expires' => $token->getExpires(), 'refresh_token' => $token->getRefreshToken(), 'resource_owner_id' => $token->getResourceOwnerId()];

		$serialized_token = serialize($token);

		$existing = TokenStorage::orderBy('id', 'desc')->first();

		if ($existing) {
			$existing->object = $serialized_token;
			$existing->save();
		} else {
			$new = new TokenStorage;
			$new->object = $serialized_token;
			$new->save();
		}

	}


    public function index()
    {

	    $provider = new \Dilab\OAuth2\Client\Provider\Envato([
		    'clientId'          => 'codechecker-ejjcofsz',
		    'clientSecret'      => 'u9XkYO4DiaUyIrTdFp3d3YMBDbGGZ1qd',
		    'redirectUri'       => 'http://codecheck.dev/home',
	    ]);


		 $token = $this->getAccessTokenFromDataBase();


		    if (isset($token) && !$token->hasExpired()) {

			    try {

				    if (isset($_GET['purchasecode'])) {
					    $data = $provider->getBuyerStatusByCode($_GET['purchasecode'], $token);
					    if (!array_key_exists('error', $data )) return view('home', ['data' => $data]);
					    else return view('home', ['error' => $data]);
				    } else return view('home');


			    } catch (Exception $e) {

				    exit($e->getMessage());
			    }
		    }

		 elseif ( isset($token) && $token->hasExpired() ) {

			    $new_token = $provider->getAccessToken('refresh_token', [
				    'refresh_token' => $token->getRefreshToken()
			    ]);

			    $token_to_save = new AccessToken([
				    'access_token' => $new_token->getToken(),
				    'resource_owner_id' => $new_token->getResourceOwnerId(),
		            'refresh_token' => $token->getRefreshToken(),
					'expires' => $new_token->getExpires()
				    ]
		        );

			 try {

				 if (isset($_GET['purchasecode'])) {
					 $data = $provider->getBuyerStatusByCode($_GET['purchasecode'], $token_to_save);
					 if (!array_key_exists('error', $data )) return view('home', ['data' => $data]);
					 else return view('home', ['error' => $data]);
				 } else return view('home');


			 } catch (Exception $e) {

				 exit($e->getMessage());
			 }

			    $this->saveAccessTokenToDataBase($token_to_save);

		 } elseif ( isset($_GET['code']) ) {

		        $token = $provider->getAccessToken('authorization_code', [
			        'code' => $_GET['code']
		        ]);

				$this->saveAccessTokenToDataBase($token);


			    header('Location: /');
			    exit;

		    } else {
			    $authUrl = $provider->getAuthorizationUrl();
			    header('Location: '.$authUrl);
			    exit;
		    }

    }
}
