<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for initializing logger
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Application_Resource_Acl extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Acl
     */
    public function init()
    {
        return $this->getAcl();
    }
    
    /**
     * Attach acl
     *
     * @param  Zend_Acl $Acl
     * @return Zend_Application_Resource_Acl
     */
    public function setAcl(Zend_Acl $acl)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Retrieve acl object
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        if (null === $this->_acl) {
            $config = $this->getOptions();
            if ($config instanceof Zend_Config) {
                $config = $config->toArray();
            }

            if (!is_array($config) || empty($config)) {
                /** @see Zend_Log_Exception */
                require_once 'Zend/Log/Exception.php';
                throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
            }

            if (array_key_exists('className', $config)) {
                $class = $config['className'];
                unset($config['className']);
            } else {
                $class = __CLASS__;
            }

            $acl = new $class;

            if (!$acl instanceof Zend_Acl) {
                /** @see Zend_Log_Exception */
                require_once 'Zend/Log/Exception.php';
                throw new Zend_Log_Exception('Passed className does not belong to a descendant of Zend_Acl');
            }
            
            //var_dump($config);
            if (!empty($config['roles'])) {
                $acl->addRoles($config['roles']);
            }
            if (!empty($config['resources'])) {
                $acl->addResources($config['resources']);
            }
            
            //$dictionary = new Application_Model_Dictionary_Table();
            //$roleList = $dictionary->getAll(array('parentid' => 1));
            //foreach ($roleList as $role) {
            //    $acl->addRole(new Zend_Acl_Role($role['acronym']));
            //}
            
            $this->setAcl($acl);
        }
        
        return $this->_acl;
    }
    
}