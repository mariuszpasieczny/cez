<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    
    protected $_auth = null;
    protected $_acl = null;
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_auth = Zend_Auth::getInstance();
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_acl = $bootstrap->getResource('acl');
        
        if ($this->_auth->hasIdentity())
        {
            $identity = $this->_auth->getIdentity();
            $role = strtolower($identity->role);
        }
        else
        {
            $role = 'guest';
        }
        
        $controller = $request->controller;
        $action = $request->action;
        
        if(!$this->_acl->isAllowed($role, $controller, $action))
        {
            if('guest' == $role)
            {
                $request->setModuleName('default');
                $request->setControllerName('auth');
                $request->setActionName('login');
            }
            else
            {
                $request->setModuleName('default');
                $request->setControllerName('error');
                $request->setActionName('noauth');
            }
        }
    }       
    
    public function getAcl()
    {
        return $this->_acl;
    }
}