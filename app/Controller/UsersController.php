<?php

class UsersController extends AppController {
    public $components = array('Openid', 'RequestHandler');
    public $uses = array();

    public function index() {
	        $this->User->recursive = 0;
	        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
	        $this->User->id = $id;
	        if (!$this->User->exists()) {
	            throw new NotFoundException(__('Invalid user'));
	        }
	        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
	        if ($this->request->is('post')) {
	            $this->User->create();
	            if ($this->User->save($this->request->data)) {
	                $this->Session->setFlash(__('The user has been saved'));
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }
	        }
    }

    public function edit($id = null) {
	        $this->User->id = $id;
	        if (!$this->User->exists()) {
	            throw new NotFoundException(__('Invalid user'));
	        }
	        if ($this->request->is('post') || $this->request->is('put')) {
	            if ($this->User->save($this->request->data)) {
	                $this->Session->setFlash(__('The user has been saved'));
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
	            }
	        } else {
	            $this->request->data = $this->User->read(null, $id);
	            unset($this->request->data['User']['password']);
	        }
    }

    public function delete($id = null) {
	        if (!$this->request->is('post')) {
	            throw new MethodNotAllowedException();
	        }
	        $this->User->id = $id;
	        if (!$this->User->exists()) {
	            throw new NotFoundException(__('Invalid user'));
	        }
	        if ($this->User->delete()) {
	            $this->Session->setFlash(__('User deleted'));
	            $this->redirect(array('action'=>'index'));
	        }
	        $this->Session->setFlash(__('User was not deleted'));
	        $this->redirect(array('action' => 'index'));
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
        $this->Openid->authenticate($openid, $returnTo, $realm);
    }

    private function handleOpenIDResponse($returnTo) {

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

    public function strip_cdata($string) {
			    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
			    return str_replace($matches[0], $matches[1], $string);
	}
}

?>