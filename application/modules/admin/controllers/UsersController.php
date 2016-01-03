<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/IndexController.php
 
class Admin_UsersController extends Application_Controller_Abstract
{
    
    protected $_users;
 
    public function init()
    {
        /* Initialize action controller here */
        $this->_users = new Application_Model_Users_Table();
        $this->_users->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_users->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('lastname','firstname'));
        parent::init();
        
        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', array('html', 'json', 'xls'))
            ->addActionContext('edit', 'html')
            ->addActionContext('set', 'html')
            ->addActionContext('change-password', 'html')
            ->addActionContext('account', 'html')
            ->setSuffix('html', '')
            ->initContext();
        
        if ($context->getCurrentContext() == 'xls') {
            $this->_users->setItemCountPerPage(null);
        }
    }
    
    public function listAction()
    {
        $this->_forward('index');
    }
 
    public function indexAction()
    {
        // action body
        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $request = $this->getRequest();
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_users->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        $columns = array('lastname', 'firstname', 'symbol', 'email', 'phoneno', 'role', 'status');
        if ($this->_auth->getIdentity()->role == 'superadmin')
            array_unshift ($columns, 'region');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_users->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_users->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_users->getItemCountPerPage());
        $this->_users->setLazyLoading(false);
        $this->view->users = $this->_users->getAll($request->getParams());
        $this->view->paginator = $this->_users->getPaginator();
        $this->view->request = $request->getParams();
        $roles = $this->_config->get('production')->get('resources')->get('acl')->get('roles')->toArray();
        $this->view->roles = array_keys($roles);
        if ($regions = $this->_config->get('production')->get('regions'))
            $this->view->regions = array_keys($regions->toArray());
        $statuses = $this->_dictionaries->getStatusList('users');
        $this->view->statuses = $statuses;
        
        $this->view->filepath = '/../data/temp/';
        $this->view->filename = 'Lista uzytkownikow-' . date('YmdHis') . '.xlsx';
    }
 
    public function editAction()
    {
        // action body
        
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form    = new Application_Form_User();
        $roles = $this->_config->get('production')->get('resources')->get('acl')->get('roles')->toArray();
        $roles = array_keys($roles);
        if ($schema = $request->getParam('region')) {
            $this->_users->setSchema($this->_config->get('production')->get('regions')->get($schema));
        }
        if ($id) {
            $user = $this->_users->get($id);
            $form->setDefaults($user->toArray());
            if ($schema)
                $form->setDefault('region', $schema);
            //$form->getElement('region')->setAttrib('disabled', 'disabled');
        } else {
            $roles = array_filter($roles, function($value) {
                return $value != 'superadmin';
            });
        }
        if ($this->_auth->getIdentity()->role != 'superadmin') {
            $form->removeElement('region');
        }
        $regions = $this->_config->get('production')->get('regions');
        $form->setOptions(array('roles' => $roles,
            'regions' => $regions ? array_keys($regions->toArray()) : null));
        $this->view->form = $form;
 
        if ($this->getRequest()->isPost()) {
            //$validator = $form->getElement('password')->getValidator('identical');
            //$validator->setToken($this->_request->getPost('verifypassword'));
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                //if (!$values['changepassword'] && $user) {
                //    $values['password'] = $user->password;
                //} else {
                //    $values['password'] = md5($values['password']);
                //}
                if ($id) {
                    $user->setFromArray($values);
                } else {
                    $user = $this->_users->createRow($values);
                    $user->id = null;
                }
                //if (!$values['active']) {
                //    $status = $this->_dictionaries->getStatusList('users')->find('inactive', 'acronym');
                //}
                //$user->statusid = $status->id;
                $user->save();
                $this->view->success = 'Użytkownik zapisany';
            }
        }
    }    
    
    public function accountAction() 
    {
        // action body
        $request = $this->getRequest();
        
    }  
    
    public function setAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if ($schema = $request->getParam('region')) {
            $this->_users->setSchema($this->_config->get('production')->get('regions')->get($schema));
            $this->_dictionaries->setSchema($this->_config->get('production')->get('regions')->get($schema));
        }
        $user = $this->_users->get($id);
        if (!$user) {
            throw new Exception('Nie znaleziono użytkownika');
        }
        $form = new Application_Form();
        $form->setDefaults($user->toArray());
        $this->view->form = $form;
        $this->view->user = $user;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $params = $request->getParams();
                $this->_dictionaries->clearCache();
                if (!empty($params['active'])) {
                    $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
                } else {
                    $status = $this->_dictionaries->getStatusList('users')->find('inactive', 'acronym');
                }
                $user->statusid = $status->id;
                $user->modifieddate = date('Y-m-d H:i:s');
                $user->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                if (!empty($params['active']) && !$user->password) {
                    $user->repasshash = md5(time());
                    $user->save();
                    $mail = new Zend_Mail('UTF-8');
                    $mail->setFrom($this->_config->get(APPLICATION_ENV)->comments->mail->from);
                    $mail->addTo($user->email, "{$user->lastname} {$user->firstname}");
                    $mail->setSubject('Aktywacja konta w systemie CEZ Nplay');
                    if ($schema) {
                        $host = $schema . '.cez.nplay.pl';
                    } else {
                        $host = $_SERVER['SERVER_NAME'];
                    }
                    $html = 'Witaj ' . $user->firstname . '<br><br>'
                            . 'Przejdź na stronę <a href="http://' . $host . '/auth/change-password/hash/' . $user->repasshash . '">' . $host . '</a> by ustawić hasło.';
                    $mail->setBodyHtml($html);
                    $mail->send();
                }
                $this->view->success = 'Użytkownik został zmodyfikowany';
            }
        }
    } 
    
    public function changePasswordAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if ($schema = $request->getParam('region')) {
            $this->_users->setSchema($this->_config->get('production')->get('regions')->get($schema));
            $this->_dictionaries->setSchema($this->_config->get('production')->get('regions')->get($schema));
        }
        $user = $this->_users->get($id);
        if (!$user) {
            throw new Exception('Nie znaleziono użytkownika');
        }
        $form = new Application_Form_User_Password();
        $form->setDefaults($user->toArray());
        $this->view->form = $form;
        $this->view->user = $user;

        if ($this->getRequest()->isPost()) {
            $validator = $form->getElement('password')->getValidator('identical');
            $validator->setToken($this->_request->getPost('verifypassword'));
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if ($values['score'] <= 0) {
                    $form->setDescription('Hasło jest zbyt słabe');
                    return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                $user->password = md5($values['password']);
                $user->modifieddate = date('Y-m-d H:i:s');
                $user->save();
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Hasło zostało zmienione';
            }
        }
    }
    
}