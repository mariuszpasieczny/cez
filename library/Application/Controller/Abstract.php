<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/AbstractController.php
 
class Application_Controller_Abstract extends Zend_Controller_Action
{
    
    protected $_auth;
    protected $_acl;
    protected $_config;
    protected $_dictionaries;
 
    public function init()
    {
        /* Initialize action controller here */
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        
        $this->_auth = Zend_Auth::getInstance();
        $this->_acl = $bootstrap->getResource('acl');
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        
        $this->view->auth = $this->_auth->getIdentity();
        
        $this->_dictionaries = new Application_Model_Dictionaries_Table();
        $this->_dictionaries->setItemCountPerPage(Application_Db_Table::ITEMS_PER_PAGE);
        $this->_dictionaries->setOrderBy('acronym');
        
        $this->view->request = $this->getRequest()->getParams();
        
        $form    = new Application_Form_Auth();
        $this->view->form = $form;
        
        $types = $this->_dictionaries->getTypeList('service');
        $this->view->types = $types;
        
        $this->view->version = file_get_contents(APPLICATION_PATH . '/version');
    }

}