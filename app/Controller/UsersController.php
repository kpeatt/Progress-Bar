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

		  	echo $steam_name.'<br>';
		  	echo $steam_realname;

		  	$this->User->create();
		  	
		  		$this->User->steam_id = $steam_id;
				$this->User->steam_name = $steam_name;
				$this->User->steam_realname = $steam_realname;
				$this->User->steam_customurl = $steam_customurl;
				$this->User->steam_avatar = $steam_avatar;
				$this->User->steam_avatar_med = $steam_avatar_med;
				$this->User->steam_avatar_full = $steam_avatar_full;
				$this->User->steam_state = $steam_state;
				$this->User->steam_loc_country = $steam_loc_country;
				$this->User->steam_loc_state = $steam_loc_state;
				$this->User->steam_loc_cityid = $steam_loc_cityid;
				$this->User->steam_lastlogoff = $steam_lastlogoff;
				$this->User->steam_lastlogoff = $steam_membersince;
				
	        $this->User->save();

        }
    }

    public function strip_cdata($string) {
			    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches);
			    return str_replace($matches[0], $matches[1], $string);
	}
}

?>