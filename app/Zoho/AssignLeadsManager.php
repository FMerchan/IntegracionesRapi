<?php

namespace App\Zoho;

use Illuminate\Support\Facades\Log;
use App\Zoho\LeadsDAO;


class AssignLeadsManager
{
    private $org;


    public function __construct(Organization $org)
    {
        $this->org = $org;

    }

    public function assignLeads($leads, $users){
    	Log::debug('AssignLeadsManager');
        $qtyAssigned = 0;

    	foreach ($leads as $lead) {
            $brand = $this->getLeadBrand($lead);
            if ($brand != null){
                $owner = $this->getBrandOwner($brand);
                if ($owner != null){
                    $this->assignLead($lead, $owner);
                    $qtyAssigned++;
                }
            }
        }

        $qtyLeads = count($leads);

        return $qtyAssigned.' leads de '.$qtyLeads.' fueron asignados';
    	
    }

    private function getLeadBrand($lead){
        return $lead['Brand'];
    }

    private function getBrandOwner($brand){
        $leadsDAO = new LeadsDAO($this->org);
        $allLeads = $leadsDAO->getAll();
        $ownerFound = false;
        $owner = null;

        for ($i=0; $i<count($allLeads) && !$ownerFound; $i++){
            if ($allLeads[$i]['Brand'] != '' && $allLeads[$i]['Brand']['id'] == $brand['id'] && $allLeads[$i]['Owner']['id'] != $this->org->getAdminId()){
                $ownerFound = true;
                $owner = $allLeads[$i]['Owner'];
                Log::debug($allLeads[$i]['Owner']['id'].'!='.$this->org->getAdminId());
                
            }
        }

        return $owner;
    }

    private function assignLead($lead, $owner){

        $leadsDAO = new LeadsDAO($this->org);
        $params = array();
        $params['Owner'] = $owner;
        $leadsDAO->update($lead['id'], $params);
    }
}
