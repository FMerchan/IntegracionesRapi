<?php

namespace App\Zoho;

use App\Zoho\Organization;
use App\Zoho\TokenManager;
use Illuminate\Support\Facades\Log;

class UsersDAO {

	private $org;
	private $url = "https://www.zohoapis.com/crm/v2/users";

	public function __construct(Organization $org)
	{
	    $this->org = $org;

	}
	  
	public function getAllHunters(){
		$moreRecords = true;
		$page = 1;
		$allHunters = array();

		while ($moreRecords){
			$accessToken = TokenManager::getAccessToken($this->org);
			$params = http_build_query(
						array(
							'page' => $page));
			$opts = [
			    "http" => [
			        "method" => "GET",
			    	"header" =>"Authorization: Zoho-oauthtoken ".$accessToken."\r\n" .
			              "Content-Type: application/x-www-form-urlencoded;charset=UTF-8\r\n",
			        "content" => $params
			    ]
			];

			$context = stream_context_create($opts);
			$search = '/search?criteria=(Tipo_Hunter:starts_with:A*)';
			$responseJson = file_get_contents($this->url.$search, false, $context);
			$responseArray = json_decode($responseJson, true);
			$allHunters = array_merge($allHunters, $responseArray['users']);
			$moreRecords = $responseArray['info']['more_records'];
			$page++;
		}
		
		return $allHunters;
 	}

 	
}