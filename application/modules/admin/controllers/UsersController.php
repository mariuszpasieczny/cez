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
        $this->_users->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'lastname DESC');
        parent::init();
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->addActionContext('edit', 'html')
            ->addActionContext('account', 'html')
            ->setSuffix('html', '')
            ->initContext();
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
        $columns = array('firstname','lastname','email','role');
        /*if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_users->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_users->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }*/
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_users->getItemCountPerPage());
        $this->_users->setLazyLoading(false);
        $this->view->users = $this->_users->getAll();
        $this->view->paginator = $this->_users->getPaginator();
        $this->view->request = $request->getParams();
    }
 
    public function editAction()
    {
        // action body
        
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form    = new Application_Form_User();
        $form->setOptions(array('roles' => $this->_config->get('production')->get('resources')->get('acl')->get('roles')));
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        if ($id) {
            $user = $this->_users->get($id);
            $form->setDefaults($user->toArray());
        }
        $this->view->form = $form;
 
        if ($this->getRequest()->isPost()) {
            $validator = $form->getElement('password')->getValidator('identical');
            $validator->setToken($this->_request->getPost('verifypassword'));
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if (!$values['changepassword'] && $user) {
                    $values['password'] = $user->password;
                } else {
                    $values['password'] = md5($values['password']);
                }
                if ($id) {
                    $user->setFromArray($values);
                } else {
                    $user = $this->_users->createRow($values);
                    $user->id = null;
                }
                if (!$values['active']) {
                    $status = $this->_dictionaries->getStatusList('users')->find('inactive', 'acronym');
                }
                $user->statusid = $status->id;
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
    
}