<?php

class UsersController extends AppController {
    public $components = array('Openid', 'RequestHandler', 'Auth', 'Cookie', 'Session');
    public $uses = array('User');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->loginRedirect = $this->__getRedirectUrl();
		$this->Auth->allow('index', 'login', 'register', 'newpassword', 'setnewpassword');
//		$this->__setupLayout();
	}

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
        
        if(!empty($this->data)) {
        	if ($this->RequestHandler->isPost() && !$this->Openid->isOpenIDResponse()) {
            	$this->User->makeOpenIDRequest($this->data['OpenidUrl']['openid'], $returnTo, $realm);
        	} elseif ($this->Openid->isOpenIDResponse()) {
            	$this->User->handleOpenIDResponse($returnTo);
        	}
		}

        
    }
    
    public function logout() {
    	$this->Auth->logout();
		$this->redirect('/users/login');
    }
    
    public function dashboard() {
    }
	
	function __getRedirectUrl() {
   		if($this->Session->check('users.redirect')) {
   			$redirectUrl = $this->Session->read('users.redirect');
   		} else {
   			$redirectUrl = '/users/dashboard';
   		}
   		return $redirectUrl;
   	}
}

?>