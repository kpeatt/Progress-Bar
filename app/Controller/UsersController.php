<?php

class UsersController extends AppController {
    public $components = array('Openid', 'RequestHandler');
    public $uses = array();

    public function strip_cdata($string) { 
		    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches); 
		    return str_replace($matches[0], $matches[1], $string); 
		}

    public function login() {
        $realm = 'http://'.$_SERVER['HTTP_HOST'];
        $returnTo = $realm . '/users/login';

        if ($this->RequestHandler->isPost() && !$this->Openid->isOpenIDResponse()) {
            $this->makeOpenIDRequest($this->data['OpenidUrl']['openid'], $returnTo, $realm);
        } elseif ($this->Openid->isOpenIDResponse()) {
            $this->handleOpenIDResponse($returnTo);
        }
    }
    
    private function makeOpenIDRequest($openid, $returnTo, $realm) {
        $required = array('email');
        $optional = array('nickname');
        $this->Openid->authenticate($openid, $returnTo, $realm, array('sreg_required' => $required, 'sreg_optional' => $optional));
    }
    
    private function handleOpenIDResponse($returnTo) {
        
        $apiKey = "4025BCF7889FDAE9DC651ECE0EC4022E";

        $response = $this->Openid->getResponse($returnTo);

        if ($response->status == Auth_OpenID_SUCCESS) {
			
			$params = $this->params['url'];

            echo "Success!<br>";

            preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $_GET['openid_claimed_id'], $matches);
			$steamID = is_numeric($matches[1]) ? $matches[1] : 0;
			
			$userinfo = simplexml_load_file("http://steamcommunity.com/profiles/".$steamID."/?xml=1");
			$apiuserinfo = simplexml_load_file("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$apiKey."&steamids=".$steamID."&format=xml");
			
			$steam_name = $this->strip_cdata($userinfo->steamID);
		  	$steam_realname = $apiuserinfo->players->player->realname;
		  	$steam_avatar = $this->strip_cdata($userinfo->avatarIcon);
		  	$steam_avatar_med = $this->strip_cdata($userinfo->avatarMedium);
		  	$steam_avatar_full = $this->strip_cdata($userinfo->avatarFull);
		  	$steam_customURL = $this->strip_cdata($userinfo->customURL);
		  	$steam_membersince = $apiuserinfo->players->player->timecreated;
		  	$steam_lastlogoff = $apiuserinfo->players->player->lastlogoff;
		  	$steam_loc_country = $apiuserinfo->players->player->loccountrycode;
		  	$steam_loc_state = $apiuserinfo->players->player->locstatecode;
		  	$steam_loc_cityid = $apiuserinfo->players->player->loccityid;
		  	
		  	echo $steam_name;
			
        }
    }
}

?>