<?php

namespace App\Zoho;

use App\Zoho\Organization;
use App\Zoho\TokenManager;
use Illuminate\Support\Facades\Log;

class LeadsDAO {

	private $org;
	private $url = "https://www.zohoapis.com/crm/v2/Leads";
	private $allLeads = null;

	public function __construct(Organization $org)
	{
	    $this->org = $org;

	}
	  
	public function getAllUnassigned(){
		return $this->filterUnassigned($this->getAll());
 	}

 	public function getAll(){
 		if ($this->allLeads != null){
 			return $this->allLeads;
 		}
 		$moreRecords = true;
		$page = 1;
		$allLeads = array();

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
			$responseJson = file_get_contents($this->url, false, $context);
			$responseArray = json_decode($responseJson, true);
			$allLeads = array_merge($allLeads, $responseArray['data']);
			$moreRecords = $responseArray['info']['more_records'];
			$page++;
		}
		
		return $allLeads;
 	}

 	public function filterUnassigned($leads){
 		$filteredArray = array();

 		foreach($leads as $lead){
		  if ($lead['Owner']['id'] == $this->org->getAdminId()){
		  	array_push($filteredArray, $lead);
		  }
		}

		return $filteredArray;
 	}

 	public function update($idLead, $paramsMap){

 		$accessToken = TokenManager::getAccessToken($this->org);

 		$data = array();
 		$arrayData = array();
 		$arrayData[0] = $paramsMap;
 		$data['data'] = $arrayData;
 		$dataJson = json_encode($data);
 		Log::debug('data '.$dataJson);
		$opts = [
		    "http" => [
		        "method" => "PUT",
		    	"header" =>"Authorization: Zoho-oauthtoken ".$accessToken."\r\n" .
		              "Content-Type: application/x-www-form-urlencoded;charset=UTF-8\r\n",
		         "content" => $dataJson
		    ]
		];

		$context = stream_context_create($opts);
		
		$responseJson = file_get_contents($this->url.'/'.$idLead, false, $context);
		Log::debug('update response: $responseJson');
		
 	}
}