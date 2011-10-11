<?php

class UsersController extends AppController {
    public $components = array('Openid', 'RequestHandler');
    public $uses = array();

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
        $response = $this->Openid->getResponse($returnTo);

        if ($response->status == Auth_OpenID_SUCCESS) {
            $sregResponse = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
            $sregContents = $sregResponse->contents();
            
            echo "Success!<br>";

            debug($sregContents);
        }
    }
}

?>