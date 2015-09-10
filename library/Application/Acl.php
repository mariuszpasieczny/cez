<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @see Zend_Acl
 */
require_once 'Zend/Acl.php';


/**
 * Resource for initializing logger
 *
 * @uses       Zend_Acl
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Application_Acl extends Zend_Acl
{
    
    public function addRoles($roles) {
        foreach ($roles as $name => $parents) {
            if (!$this->hasRole($name)) {
                if (empty($parents)) {
                    $parents = null;
                } else {
                    $parents = explode(',', $parents);
                }
                $this->addRole(new Zend_Acl_Role($name), $parents);             
            }
        }       
    }

    public function addResources($resources) {
        foreach ($resources as $permissions => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ($controller == 'all') {
                    $controller = null;
                } else {
                    if (!$this->has($controller)) {
                        $this->add(new Zend_Acl_Resource($controller));
                    }
                }
                if (!is_array($actions)) {
                    continue;
                }
                foreach ($actions as $action => $roles) {
                    if ($action == 'all') {
                        $action = null;
                    }
                    foreach (explode(',', $roles) as $role) {
                        if ($permissions == 'allow') {
                            $this->allow($role, $controller, $action);
                        }
                        if ($permissions == 'deny') {                           
                            $this->deny($role, $controller, $action);
                        }
                    }
                }
            }
        }
    }
    
}