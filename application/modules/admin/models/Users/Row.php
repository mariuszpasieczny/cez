<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Users_Row extends Application_Db_Table_Row
{
    
        protected $_services;

        public function __get($columnName) {
        switch ($columnName) {
            //case 'role':
            //    return $this->getRole();
            //    break;
            default:
                return parent::__get($columnName);
        }
    }
    
    public function getPrivileges() {
        //return $this->getDependentRowset();
    }
    
    public function __toString() {
        return $this->lastname . ' ' . $this->firstname . ' ( ' . $this->symbol . ' )';
    }
    
    public function getServices() {
        return $this->_services = $this->findDependentRowset('Application_Model_Services_Table', 'Technician');
    }
    
}