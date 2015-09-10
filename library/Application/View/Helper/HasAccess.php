<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_View_Helper_HasAccess extends Zend_View_Helper_Abstract
{
    private $_acl;
    
    public function hasAccess($role, $controller, $action = null)
    {
        if (!$this->_acl) {
            $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $this->_acl = $bootstrap->getResource('acl'); 
            //In yout case registry, but front controller plugin is better way to implement ACL
        }
        return $this->_acl->isAllowed($role, $controller, $action);
    }
}