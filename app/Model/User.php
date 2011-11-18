<?php
// app/Model/User.php
class User extends AppModel {
    public $name = 'User';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'steam_id' => array(
        ),
        'steam_name' => array(
        ),
        'steam_realname' => array(
        ),
        'steam_customURL' => array(
        ),
        'steam_avatar' => array(
        ),
        'steam_avatar_med' => array(
        ),
        'steam_avatar_full' => array(
        ),
        'steam_state' => array(
        ),
        'steam_loc_country' => array(
        ),
        'steam_loc_state' => array(
        ),
        'steam_loc_cityid' => array(
        ),
        'steam_lastlogoff' => array(
        ),
        'steam_membersince' => array(
        )
    );
    
    public function makeOpenIDRequest($openid, $returnTo, $realm) {
        $this->Openid->authenticate($openid, $returnTo, $realm);
    }
    
    public function handleOpenIDResponse($returnTo) {

        $apiKey = "4025BCF7889FDAE9DC651ECE0EC4022E";

        $response = $this->Openid->getResponse($returnTo);

        if ($response->status == Auth_OpenID_SUCCESS) {

            echo "Success!<!-- <br> -->";

            preg_match("#^http://steamcommunity.com/openid/id/([0-9]{17,25})#", $_GET['openid_claimed_id'], $matches);
			$steamID = is_numeric($matches[1]) ? $matches[1] : 0;
			
			$this->User->steam_id = $steamID;
			
			if (!$this->User->exists()) {
			
				$userinfo = simplexml_load_file("http://steamcommunity.com/profiles/".$steamID."/?xml=1");
				$userinfo = Xml::toArray($userinfo);
				
				$apiuserinfo = simplexml_load_file("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$apiKey."&steamids=".$steamID."&format=xml");
				$apiuserinfo = Xml::toArray($apiuserinfo);
				
				$data = array(
					'steam_id' => $steamID,
					'steam_name' => $userinfo['profile']['steamID'],
					'steam_realname' => $userinfo['profile']['realname'],
					'steam_avatar' => $userinfo['profile']['avatarIcon'],
				  	'steam_avatar_med' => $userinfo['profile']['avatarMedium'],
				  	'steam_avatar_full' => $userinfo['profile']['avatarFull'],
				  	'steam_state' => $userinfo['profile']['stateMessage'],
				  	'steam_customURL' => $userinfo['profile']['customURL'],
				  	'steam_membersince' => $apiuserinfo['response']['players']['player']['timecreated'],
				  	'steam_lastlogoff' => $apiuserinfo['response']['players']['player']['lastlogoff'],
				  	'steam_loc_country' => $apiuserinfo['response']['players']['player']['loccountrycode'],
				  	'steam_loc_state' => $apiuserinfo['response']['players']['player']['locstatecode'],
				  	'steam_loc_cityid' => $apiuserinfo['response']['players']['player']['loccityid']
				);
	
			  	debug($data);
	
			  	$this->User->create();
					
		        if($this->User->save($data)) {
		        	$this->set('error', 'This user has been saved');
		       	}
	       
	       } else {
	       
		       	$userinfo = simplexml_load_file("http://steamcommunity.com/profiles/".$steamID."/?xml=1");
				$userinfo = Xml::toArray($userinfo);
				
				$apiuserinfo = simplexml_load_file("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$apiKey."&steamids=".$steamID."&format=xml");
				$apiuserinfo = Xml::toArray($apiuserinfo);
				
				$data = array(
				  	'steam_state' => $userinfo['profile']['stateMessage'],
				  	'steam_lastlogoff' => $apiuserinfo['response']['players']['player']['lastlogoff'],
				);
				
				if($this->User->save($data)) {
		        	$this->set('error', 'This user already existed and has been updated');
		       	}
	       
	       }
        }
    }

}