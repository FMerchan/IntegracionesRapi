<?php
namespace App\Zoho;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Zoho\Organization;
use Storage;

class TokenManager{

	private static $tokenURL = "https://accounts.zoho.com/oauth/v2/token";
	private static $tokenMap;

	public static function getAccessToken(Organization $org){
		Log::debug("Checking credentials");
		
		if (TokenManager::tokenEmptyOrExpired($org)){
			Log::debug("Generating new access token");
			$params["refresh_token"] = $org->getRefreshToken();
			$params["client_id"] = $org->getClientId();
			$params["client_secret"] = $org->getClientSecret();
			$params["redirect_uri"] = $org->getRedirectURI();
			$params["grant_type"] = "refresh_token";
			
			$url = TokenManager::$tokenURL."?" . http_build_query($params);

			Log::debug($url);

			$opts = [
			    "http" => [
			        "method" => "POST"
			    ]
			];

			$context = stream_context_create($opts);
			$responseJson = file_get_contents($url, false, $context);
			$responseArray = json_decode($responseJson, true);

			Log::debug($responseArray);
			$org->setAccessToken($responseArray['access_token']);
			$org->setAccessTokenDuration($responseArray['expires_in']);
			TokenManager::persistToken($org->getAccessToken(), $org->getAccessTokenDuration());
		} else{
			Log::debug('Token is still valid');
			$org->setAccessToken(TokenManager::$tokenMap['accessToken']);
		}

		return $org->getAccessToken();

	}

	private static function tokenEmptyOrExpired(Organization $org){
		$content = Storage::get('token.txt');
		$lines = explode("\n", $content);
		foreach($lines as $line){
		  $keyValue = explode("=", $line);
		  TokenManager::$tokenMap[$keyValue[0]] = $keyValue[1];
		}
		
		if (TokenManager::$tokenMap['accessToken'] == '' ||
			TokenManager::$tokenMap['accessTokenStart'] == '' ||
			TokenManager::$tokenMap['accessTokenDuration'] == ''){
			Log::debug('empty');
			return true;
		}

		$timeStart = strtotime(TokenManager::$tokenMap['accessTokenStart']);
		$duration = intval(TokenManager::$tokenMap['accessTokenDuration']);

		$now = now();
		$dateUntil = Carbon::parse($timeStart)->addSeconds($duration);
		Log::debug("Now: ".$now);
		Log::debug("Until: ".$dateUntil);
		return $now->gte($dateUntil);
	}

	private static function persistToken($accessToken, $duration){
		$content = 'accessToken='.$accessToken.PHP_EOL;
		$content .= 'accessTokenStart='.now().PHP_EOL;
		$content .= 'accessTokenDuration='.$duration;
		Storage::put('token.txt', $content);
	}
}