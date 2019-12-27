<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Zoho\LeadsDAO;
use App\Zoho\UsersDAO;
use App\Zoho\Organization;
use App\Zoho\AssignLeadsManager;

class AssignLeadsController extends Controller
{
    public function assignLeads(Request $request){
    	Log::debug('assignLeads');

    	$org = new Organization($request['orgId'], $request['clientId'], $request['clientSecret'], $request['refreshToken'], $request['redirectURI'],
    		$request['adminId']);

    	$leadsDAO = new LeadsDAO($org);
    	$usersDAO = new UsersDAO($org);

    	$assignLeadsMgr = new AssignLeadsManager($org);
    	return $assignLeadsMgr->assignLeads($leadsDAO->getAllUnassigned(),
    								 $usersDAO->getAllHunters());
    	
    }
}
