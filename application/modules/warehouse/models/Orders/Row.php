<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Orders_Row extends Application_Db_Table_Row
{
    
    public function getProducts($params = null) {
        $rowset = parent::findDependentRowset('Application_Model_Orders_Lines_Table');
        if (!$params) {
            return $rowset;
        }
        return $rowset->filter($params);
    }
    
    public function getStatus() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }
    
    public function getTechnician() {
        return parent::findParentRow('Application_Model_Users_Table');
    }
    
    public function isAssigned() {
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('assigned', 'acronym');
        
        return $this->technicianid && $this->statusid == $status->id ? true : false;
    }
    
    public function isNew() {
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('new', 'acronym');
        
        return !$this->technicianid && $this->statusid == $status->id ? true : false;
    }
    
}