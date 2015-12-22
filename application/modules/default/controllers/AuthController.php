<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/AuthController.php

class AuthController extends Application_Controller_Abstract {

    public function init() {
        /* Initialize action controller here */
        parent::init();

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('login', 'html')
                ->setSuffix('html', '')
                ->initContext();
    }

    public function indexAction() {
        $this->_forward('login');
    }

    public function loginAction() {
        // action body

        $request = $this->getRequest();
        $form = new Application_Form_Auth();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                // Get our authentication adapter and check credentials
                $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                $adapter = new Zend_Auth_Adapter_DbTable(
                        Zend_Db_Table::getDefaultAdapter(), 'users', 'email', 'password', "? AND statusid = '{$status->id}'"
                );
                $login = $request->getParam('email');
                $password = $request->getParam('password');
                $adapter->setIdentityColumn('email')->setIdentity($login)->setCredential(md5($password));
                $result = $this->_auth->authenticate($adapter);
                if (!$result->isValid()) {
                    // Invalid credentials
                    $form->setDescription('Invalid credentials provided');
                    $this->view->form = $form;
                    $this->view->error = 'Invalid credentials provided';
                    return $this->render('login'); // re-render the login form
                }
                $storage = $this->_auth->getStorage();
                $return = $adapter->getResultRowObject(array(
                    'id',
                    'email',
                    'firstname',
                    'lastname',
                    'admin',
                    'role',
                    'modifieddate'
                ));
                $storage->write($return);
                $currDate = new Zend_Date();
                $modDate = new Zend_Date($return->modifieddate);
                $difference = $currDate->sub($modDate);
                $measure = new Zend_Measure_Time($difference, Zend_Measure_Time::SECOND);
                $measure->convertTo(Zend_Measure_Time::MONTH);
                if ($this->_config->get(APPLICATION_ENV)->auth->login->expiration && $measure->getValue() > $this->_config->get(APPLICATION_ENV)->auth->login->expiration) {
                    $table = new Application_Model_Users_Table();
                    $user = $table->getAll(array('email' => $login))->current();
                    $user->repasshash = md5(time());
                    $user->save();
                    $form->setDescription('Upłynął termin ważności hasła. Kliknij <a href="http://' . $_SERVER['SERVER_NAME'] . '/auth/change-password?hash=' . $user->repasshash . '">tutaj</a> by ustawić nowe hasło.');
                    $this->view->form = $form;
                    $this->view->error = 'Upłynął termin ważności hasła';
                    Zend_Auth::getInstance()->clearIdentity();
                    return $this->render('login'); // re-render the login form
                }

                switch ($return->role) {
                    case 'admin':
                    case 'coordinator':
                    case 'technician':
                        //$this->_helper->redirector('search','services','services');
                        break;
                    case 'warehouse':
                        //$this->_helper->redirector('list','products','warehouse');
                        break;
                }

                //if ($return->role == 'admin') {
                //    $this->_helper->redirector('index','users','admin');
                //}
                //$this->_helper->redirector('account','users');
                $this->getResponse()->setHeader('REQUIRES_AUTH', 0);
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index', 'index'); // back to login page
    }

    public function remindPasswordAction() {
        // action body
        $request = $this->getRequest();
        $form = new Application_Form_RemindPassword();

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $table = new Application_Model_Users_Table();
                if (!$user = $table->getAll(array('email' => $values['email']))->current()) {
                    $form->getElement('email')->setErrors(array('email' => 'Nie znaleziono konta o podanym adresie e-mail'));
                    return;
                }
                $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                if ($user->statusid != $status->id) {
                    $form->getElement('email')->setErrors(array('email' => 'Konto zostało deaktywowane, skontaktuj się z administratorem systemu.'));
                    return;
                }
                $user->repasshash = md5(time());
                $user->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
                $transport = $bootstrap->getResource('mail');
                $mail = new Zend_Mail('UTF-8');
                $mail->setDefaultTransport($transport);
                $mail->setFrom($this->_config->get(APPLICATION_ENV)->comments->mail->from);
                $mail->addTo($user->email);
                $mail->setSubject('Zmiana hasła do konta ESOZ');
                $link = 'http://' . $_SERVER['SERVER_NAME'] . '/auth/change-password?hash=' . $user->repasshash;
                $mail->setBodyHtml("Aby dokonać jednorazowej zmiany hasła kliknij w poniższy link:<br />
$link<br /><br />

Jeśli nie zgłaszałeś chęci zmiany hasła, zignoruj ten list<br />
(prawdopodobnie inna osoba podała omyłkowo Twój adres)");
                //var_dump($mail);exit;
                $mail->send();

                $this->view->success = 'Wysłano link do zmiany hasła';
            }
        }
    }

    public function changePasswordAction() {
        // action body
        $request = $this->getRequest();
        $form = new Application_Form_ChangePassword();

        $table = new Application_Model_Users_Table();
        if (!$user = $table->getAll(array('repasshash' => $request->getParam('hash', '0')))->current()) {
            $this->view->error = 'Podany link jest nieprawidłowy';
            return;
        }
        $form->setDefault('hash', $request->getParam('hash'));

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $validator = $form->getElement('verifypassword')->getValidator('identical');
            $validator->setToken($request->getParam('password'));
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                if ($user->statusid != $status->id) {
                    $form->setDescription('Konto zostało deaktywowane, skontaktuj się z administratorem systemu.');
                    return;
                }
                $form->setDefaults($user->toArray());
                $user->password = md5($values['password']);
                $user->repasshash = null;
                $user->modifieddate = date('Y-m-d H:i:s');
                $user->save();

                $this->view->success = 'Hasło zostało zmienione';
            }
        }
    }

}
