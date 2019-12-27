<?php

namespace App\Zoho;


class Organization
{
    private $orgId;
    private $clientId;
    private $clientSecret;
    private $refreshToken;
    private $redirectURI;
    private $accessToken;
    private $accessTokenStart;
    private $accessTokenDuration;
    private $adminId;

    public function __construct($orgId, $clientId, $clientSecret, $refreshToken, $redirectURI, $adminId)
    {
        $this->setOrgId($orgId);
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);
        $this->setRefreshToken($refreshToken);
        $this->setRedirectURI($redirectURI);
        $this->setAdminId($adminId);
    }

    public function getOrgId(){
    	return $this->orgId;
    }
    public function getClientId(){
    	return $this->clientId;
    }
    public function getClientSecret(){
    	return $this->clientSecret;
    }
    public function getRefreshToken(){
    	return $this->refreshToken;
    }
    public function getAccessToken(){
    	return $this->accessToken;
    }
    public function getAccessTokenStart(){
    	return $this->accessTokenStart;
    }
    public function getAccessTokenDuration(){
    	return $this->accessTokenDuration;
    }
    public function getRedirectURI(){
    	return $this->redirectURI;
    }
    public function getAdminId(){
        return $this->adminId;
    }

    public function setOrgId($orgId){
    	$this->orgId = $orgId;
    }
    public function setClientId($clientId){
    	$this->clientId = $clientId;
    }
    public function setClientSecret($clientSecret){
    	$this->clientSecret = $clientSecret;
    }
    public function setRefreshToken($refreshToken){
    	$this->refreshToken = $refreshToken;
    }
    public function setAccessToken($accessToken){
    	$this->accessToken = $accessToken;
    }
    public function setAccessTokenStart($accessTokenStart){
    	$this->accessTokenStart = $accessTokenStart;
    }
    public function setAccessTokenDuration($accessTokenDuration){
    	$this->accessTokenDuration = $accessTokenDuration;
    }
    public function setRedirectURI($redirectURI){
    	$this->redirectURI = $redirectURI;
    }
    public function setAdminId($adminId){
        $this->adminId = $adminId;
    }
    
}
